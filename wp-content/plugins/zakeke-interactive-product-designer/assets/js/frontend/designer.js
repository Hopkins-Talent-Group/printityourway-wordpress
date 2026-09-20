function zakekeDesigner(config) {
	if (window.zakekeDesignerFirstRun) {
		return;
	}
	window.zakekeDesignerFirstRun = true;

	function getZakekeIframe() {
		return container.querySelector('iframe');
	}

	function cleanParams(params) {
		let updatedParams = Object.assign({}, params);
		delete updatedParams['save-account-details-nonce'];
		if (updatedParams['zakeke-conditions']) {
			updatedParams['zakeke-conditions'] = JSON.stringify(updatedParams['zakeke-conditions']);
		}
		return updatedParams;
	}

	function productAttributes(promiseId) {
		jQuery.ajax({
			url: config.wc_ajax_url.replace('%%endpoint%%', 'zakeke_get_attributes'),
			type: 'POST',
			data: cleanParams(config.params)
		})
			.done(function (product) {
				if (typeof product === 'string' || product instanceof String) {
					product = JSON.parse(product.trim());
				}

				const cartesian =
					a => a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));

				product.variants = product.variants.map(v =>
					v.map(o => {
						if (o.Value.Id === '') {
							return product.attributes.find(a => a.id === o.Id).values.map(v => ({
								Id: o.Id,
								Value: {
									Id: v.id
								}
							}));
						}

						return [o];
					})
				).flatMap(cartesian);

				product.variants.forEach((val, i) => {
					if (Array.isArray(val)) {
						return;
					}

					product.variants[i] = [val];
				});

				product.promiseId = promiseId;

				getZakekeIframe().contentWindow.postMessage({
					data: product,
					zakekeMessageType: 2
				}, '*');
			});
	}

	function productPrice(data) {
		const promiseId = data.promiseId;
		delete data['promiseId'];

		let params = cleanParams(Object.assign({}, config.params, data));
		delete params['save-account-details-nonce'];

		const queryString = jQuery.param(params);

		const cached = productDataCache[queryString];
		if (cached !== undefined) {
			getZakekeIframe().contentWindow.postMessage({
				data: Object.assign({
					promiseId: promiseId
				}, cached),
			zakekeMessageType: 3
			}, '*');
			return;
		}

		jQuery.ajax({
			url: config.wc_ajax_url.replace('%%endpoint%%', 'zakeke_get_price'),
			type: 'POST',
			data: params
		})
			.done(product => {
				if (typeof product === 'string' || product instanceof String) {
					product = JSON.parse(product.trim());
				}

				let productData               = {
					isOutOfStock: !(product.is_purchasable && product.is_in_stock),
					finalPrice: product.price_including_tax
				};
				productDataCache[queryString] = Object.assign({}, productData);

				productData.promiseId = promiseId;

				getZakekeIframe().contentWindow.postMessage({
					data: productData,
					zakekeMessageType: 3
				}, '*');
			});
	}

	function updatedParams(color, zakekeOptions) {
		if (color == null) {
			throw new Error('color param is null');
		}

		const colorObj = JSON.parse(color);

		let params                        = Object.assign({}, config.params);
		colorObj.forEach(val => {
			params['attribute_' + val.Id] = val.Value.Id;
		});

		if (zakekeOptions != null) {
			params = Object.assign(params, zakekeOptions);
		}

		return params;
	}

	function productData(color, zakekeOptions) {
		function emitProductDataEvent(productData) {
			getZakekeIframe().contentWindow.postMessage({
				data: productData,
				zakekeMessageType: 1
			}, '*');
		}

		const params      = cleanParams(updatedParams(color, zakekeOptions));
		const queryString = jQuery.param(params);

		const cached = productDataCache[queryString];
		if (cached !== undefined) {
			emitProductDataEvent(cached);
			return;
		}

		if (pendingProductDataRequests.indexOf(queryString) !== -1) {
			return;
		}

		pendingProductDataRequests.push(queryString);

		jQuery.ajax({
			url: config.wc_ajax_url.replace('%%endpoint%%', 'zakeke_get_price'),
			type: 'POST',
			data: params
		})
			.done(product => {
				if (typeof product === 'string' || product instanceof String) {
					product = JSON.parse(product.trim());
				}

				const productData             = {
					color: color,
					isOutOfStock: !(product.is_purchasable && product.is_in_stock),
					finalPrice: product.price_including_tax
				};
				productDataCache[queryString] = productData;
				emitProductDataEvent(productData);
			})
			.fail((request, status, error) => {
				console.log(request + ' ' + status + ' ' + error);
				const productData             = {
					color: color,
					isOutOfStock: true
				};
				productDataCache[queryString] = productData;
				emitProductDataEvent(productData);
			})
			.always(() => {
				const index = pendingProductDataRequests.indexOf(queryString);
				if (index !== -1) {
					pendingProductDataRequests.splice(index, 1);
				}
			});
	}

	function createCartSubInput(form, value, key, prevKey) {
		if (value instanceof String || typeof(value) !== 'object') {
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
		input.name  = key.replace('ztmp_prefix_', '');
		input.value = value.toString().replace(/\\/g, '');
		form.appendChild(input);
	}

	function addToCart(color, design) {
		let params = updatedParams(color, {
			'zdesign': design
		});

		delete params['variation_id'];
		delete params['zshared'];

		if (config.from_shortcode) {
			params['zakeke_return_url'] = config.wc_cart_url;
		}

		if (window.zakekeAddToCart) {
			window.zakekeAddToCart(params);
		} else {
			const form  = document.getElementById('zakeke-addtocart');
			form.method = 'POST';

			Object.keys(params).filter(x => params[x] != null).forEach(key => {
				createCartSubInput(form, params[key], key);
			});

			if (window.zakekeBeforeAddToCart) {
				window.zakekeBeforeAddToCart(design, params).then(_ => jQuery(form).submit());
			} else {
				jQuery(form).submit();
			}
		}
	}

	function addToCartAjax(data) {
		let params     = cleanParams(Object.assign({}, config.params, data));
		const form     = document.getElementById('zakeke-addtocart');
		const formData = new FormData(form);

		delete params['variation_id'];

		Object.keys(params).filter(x => params[x] != null).forEach(key => {
			if (key === 'add-to-cart') {
				key = 'ztmp_prefix_' + key;
			}
			formData.set(key, params[key]);
		});

		return jQuery.ajax({
			url: form.action,
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST'
		});
	}

	function updateCart() {
		return jQuery.ajax(
			config.wc_ajax_url.replace('%%endpoint%%', 'zakeke_update_cart')
		).then(data => {
			if (data && data.return_url) {
				window.location.href = data.return_url;
			}
		});
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

	function setAuth(customizerUrl, auth) {
		const url = new URL(customizerUrl);
		url.searchParams.set(auth.type, auth.token);
		return url.toString();
	}

	function buildSharedUrl(sharedID) {
		const url = new URL(window.location.href);
		url.searchParams.set('zshared', sharedID);
		url.searchParams.delete('zdesign_edit');

		if (!config.share_return_to_product_page) {
			url.searchParams.set('zdesign', 'new');

			if (config.params) {
				Object.keys(config.params).filter(k => k.startsWith('attribute_') || k.startsWith('ztmp_prefix_')).forEach(key => {
					url.searchParams.set(key, config.params[key]);
				});
			}
		}

		return url.toString();
	}

	var productDataCache           = {},
		pendingProductDataRequests = [],
		container                  = document.getElementById('zakeke-container');

	window.addEventListener('message', event => {
		if (event.origin !== config.zakekeUrl) {
			return;
		}

		if (event.data.zakekeMessageType === 0) {
			if (config.params.zdesign_edit) {
				updateCart();
			} else {
				addToCart(event.data.colorId, event.data.designId);
			}
		} else if (event.data.zakekeMessageType === 1) {
			let zakekeOptions = {};
			if (event.data.design.price !== undefined) {
				zakekeOptions['zakeke-price'] = event.data.design.price;
			}
			if (event.data.design.percentPrice !== undefined) {
				zakekeOptions['zakeke-percent-price'] = event.data.design.percentPrice;
			}
			if (event.data.design.conditions) {
				zakekeOptions['zakeke-conditions'] = event.data.design.conditions;
			}

			productData(event.data.design.color, zakekeOptions);
		} else if (event.data.zakekeMessageType === 2) {
			productAttributes(event.data.data.promiseId);
		} else if (event.data.zakekeMessageType === 3) {
			let zakekeOptions = {
				promiseId: event.data.data.promiseId,
				quantity: event.data.data.quantity
			};

			event.data.data.attributes.forEach(attribute => {
				zakekeOptions['attribute_' + attribute.Id] = attribute.Value.Id;
			});

			if (event.data.data.price !== undefined) {
				zakekeOptions['zakeke-price'] = event.data.data.price;
			}
			if (event.data.data.percentPrice !== undefined) {
				zakekeOptions['zakeke-percent-price'] = event.data.data.percentPrice;
			}
			if (event.data.data.conditions) {
				zakekeOptions['zakeke-conditions'] = event.data.data.conditions;
			}
			productPrice(zakekeOptions);
		} else if (event.data.zakekeMessageType === 4) {
			return addToCartAjax({
				zdesign: event.data.data.designID,
				zakeke_selections: JSON.stringify(event.data.data.attributes)
			}).then(() => {
				window.location.href = config.wc_cart_url;
			});
		} else if (event.data.zakekeMessageType === 5) {
			getZakekeIframe().contentWindow.postMessage({
				zakekeMessageType: 5,
				data: {
					promiseId: event.data.data.promiseId,
					url: buildSharedUrl(event.data.data.designDocID)
				}
			}, '*');
		} else if (event.data.zakekeMessageType === 6 || event.data.zakekeMessageType === 8) {
			return addToCartAjax({
				zdesign: event.data.data.designID,
				zakeke_namenumbers: JSON.stringify(event.data.data.attributes)
			}).then(() => {
				window.location.href = config.wc_cart_url;
			});
		}
	}, false);

	getAuthToken().then(auth => {
		const isLarge = window.matchMedia('(min-width: 769px)').matches;
		if (!isLarge && !config.from_shortcode) {
			document.body.appendChild(container);
		}
		const customizerUrl   = isLarge ? config.customizerLargeUrl : config.customizerSmallUrl;
		getZakekeIframe().src = setAuth(customizerUrl, auth);

		if (!isLarge) {
			try {
				const viewport = document.querySelector('meta[name="viewport"]');
				if (viewport) {
					viewport.content = 'width=device-width, initial-scale=1, interactive-widget=resizes-content';
				}
			} catch (e) {
				console.error(e);
			}
		}

	}).catch(() => window.history.back());
}

(() => {
	function getDesignerConfig() {
		if (window.zakekeDesignerConfig) {
			return window.zakekeDesignerConfig;
		}

		return JSON.parse(document.querySelector('#zakeke-designer-config').dataset.config);
	}

	if (window.zakekeDesignerConfig
		&& (document.readyState === 'complete'
			|| document.readyState === 'loaded'
			|| document.readyState === 'interactive')) {
		zakekeDesigner(window.zakekeDesignerConfig);
	} else {
		document.addEventListener('DOMContentLoaded', () => {
			zakekeDesigner(getDesignerConfig());
		});
	}
})();