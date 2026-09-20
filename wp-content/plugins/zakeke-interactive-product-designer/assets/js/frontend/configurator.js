function zakekeConfigurator(config) {
	function emitProductDataEvent(productData) {
		iframe.contentWindow.postMessage(productData, '*');
	}

	function isWCAttribute(attribute) {
		try {
			return JSON.parse(attribute.attributeCode).zakekePlatform && JSON.parse(attribute.optionCode).zakekePlatform;
		} catch (e) {
			return false;
		}
	}

	function toWCAttribute(attribute) {
		return {[JSON.parse(attribute.attributeCode).id]: JSON.parse(attribute.optionCode).id};
	}

	function toZakekeAttribute(attribute, option) {
		return [
			{
				id: attribute,
				isGlobal: true,
				zakekePlatform: true
		},
			{
				id: option,
				zakekePlatform: true
		}
		];
	}

	function updatedAttributes(attributes) {
		const wcAttributes = attributes.filter(isWCAttribute).reduce((acc, attribute) =>
				Object.assign(acc, toWCAttribute(attribute)),
			{});

		return Object.assign(config.attributes, wcAttributes);
	}

	function asAddToCartAttributes(attributes) {
		return Object.keys(attributes).reduce((acc, attribute) => {
			acc['attribute_' + attribute] = attributes[attribute];
			return acc;
		}, {});
	}

	function productData(messageId, attributes, compositionPrice, quantity) {
		const params = Object.assign({
			'product_id': config.product_id,
			'zakeke_price': compositionPrice
		}, config.request, asAddToCartAttributes(updatedAttributes(attributes)));

		const queryString = jQuery.param(params);
		const cached      = productDataCache[queryString];

		if (cached !== undefined) {
			emitProductDataEvent(Object.assign(cached, {
				messageId: messageId
			}));
			return;
		}

		if (pendingProductDataRequests.indexOf(queryString) !== -1) {
			return;
		}

		pendingProductDataRequests.push(queryString);

		jQuery.ajax({
			url: config.priceAjaxUrl,
			type: 'POST',
			data: params
		})
			.done(product => {
				let productMessageBody;
				if (config.integrationVersion === 1) {
					productMessageBody = product.price_including_tax;
				} else {
					productMessageBody = {
							price: product.price_including_tax,
							isOutOfStock: (!product.is_in_stock && product.is_purchasable)
					};
				}

				const productData = {
					messageId: messageId,
					zakekeMessageType: "Price",
					message: productMessageBody
				};

				productDataCache[queryString] = productData;
				emitProductDataEvent(productData);
			})
			.fail((request, status, error) => {
				console.error(request + ' ' + status + ' ' + error);
			})
			.always(() => {
				const index = pendingProductDataRequests.indexOf(queryString);
				if (index !== -1) {
					pendingProductDataRequests.splice(index, 1);
				}
			});
	}

	function createCartSubInput(form, value, key, prevKey) {
		if (value instanceof String || typeof (value) !== 'object') {
			createCartInput(form, prevKey ? prevKey + '[' + key + ']' : key, value);
		} else {
			Object.keys(value).forEach(subKey => {
				createCartSubInput(form, value[subKey], subKey, prevKey ? prevKey + '[' + key + ']' : key);
			});
		}
	}

	function createCartInput(form, key, value) {
		const input = document.createElement('INPUT');
		input.type  = 'hidden';
		input.name  = key;
		input.value = value.toString().replace(/\\/g, '');
		form.appendChild(input);
	}

	function addToCart(composition, attributes, preview, quantity, additionalProperties) {
		let params = Object.assign({
			'add-to-cart': config.product_id,
			'product_id': config.product_id
			},
			config.request,
			asAddToCartAttributes(updatedAttributes(attributes)),
			{
				'quantity': quantity,
				'zconfiguration': composition,
				'zakeke_additional_properties': JSON.stringify(additionalProperties)
			});

		const form         = document.createElement('FORM');
		form.style.display = 'none';
		form.method        = 'POST';

		delete params['variation_id'];

		params['zakeke_return_url'] = config.wc_cart_url;

		Object.keys(params).filter(x => params[x] != null).forEach(key => {
			createCartSubInput(form, params[key], key);
		});
		document.body.appendChild(form);
		jQuery(form).submit();
	}

	function getAuthToken() {
		return fetch(config.wc_ajax_url.replace('%%endpoint%%', 'zakeke_get_auth'), {
			method: 'POST'
		}).then(res => {
			if (!res.ok) {
				throw new Error('Failed to auth');
			}
			return res.json();
		}).then(res => {
			if (res.error) {
				throw new Error(res.error);
			}

			return res;
		});
	}

	function buildSharedUrl(compositionDocID) {
		const url = new URL(window.location.href);
		url.searchParams.set('zshared', compositionDocID);
		if (!config.share_return_to_product_page) {
			url.searchParams.set('zconfiguration', 'new');

			if (config.attributes) {
				Object.keys(config.attributes).filter(k => !['qty', 'token'].includes(k)).forEach(key => {
					url.searchParams.set('attribute_' + key, config.attributes[key]);
				});
			}
		}

		return url.toString();
	}

	function adjustIframeSrc(iframe) {
		try {
			new URL(iframe.src);
		} catch (e) {
			if (iframe.dataset.src) {
				iframe.src = iframe.dataset.src;
			}
		}
	}

	let sendIframeParamsInterval   = null;
	var productDataCache           = {},
		pendingProductDataRequests = [],
		container                  = document.getElementById('zakeke-configurator-container'),
		iframe                     = container.querySelector('iframe');

	adjustIframeSrc(iframe);

	var iframeOrigin = (new URL(iframe.src)).origin;

	window.addEventListener('message', event => {
		if (event.origin !== iframeOrigin) {
			return;
		}

		if (event.data.zakekeMessageType === 'AddToCart') {
			addToCart(event.data.message.composition, event.data.message.attributes, event.data.message.preview, event.data.message.quantity, event.data.message.additionalProperties);
		} else if (event.data.zakekeMessageType === 'Price') {
			productData(event.data.messageId, event.data.message.attributes, event.data.message.compositionPrice, event.data.message.quantity);
		} else if (event.data.zakekeMessageType === 'SharedComposition') {
			iframe.contentWindow.postMessage({
				messageId: event.data.messageId,
				zakekeMessageType: 'SharedComposition',
				message: {
					url: buildSharedUrl(event.data.message.compositionDocID)
				}
			}, '*');
		} else if (event.data.zakekeType === 'loaded') {
			if (!event.data.message) {
				config.integrationVersion = 1;
			}

			clearInterval(sendIframeParamsInterval);
		}
	}, false);

	getAuthToken()
		.then(data => {
			sendIframeParamsInterval = setInterval(() => {
				iframe.contentWindow.postMessage({
					type: 'load',
					parameters: Object.assign({}, data, config, {
						attributes: Object.keys(config.attributes).map(attribute =>
							toZakekeAttribute(attribute, config.attributes[attribute])
						)
					})
				}, '*');
			}, 500);
		})
		.catch(() => window.history.back());
}

if (window.zakekeConfiguratorConfig
	&& (document.readyState === 'complete'
	|| document.readyState === 'loaded'
	|| document.readyState === 'interactive')) {
	zakekeConfigurator(window.zakekeConfiguratorConfig);
} else {
	document.addEventListener('DOMContentLoaded', () => {
		zakekeConfigurator(window.zakekeConfiguratorConfig);
	});
}
