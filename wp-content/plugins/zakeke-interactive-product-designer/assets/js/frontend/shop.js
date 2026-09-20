function zakekeProductPage() {
	function handlePreviews() {
		function createPreviewWindowEl() {
			const container = document.createElement('DIV');
			container.classList.add('zakeke-cart-preview-window');
			container.style.display = 'none';

			container.appendChild(document.createElement('IMG'));

			const labelContainer = document.createElement('DIV');
			labelContainer.classList.add('zakeke-cart-preview-window-label');
			labelContainer.appendChild(document.createElement('H3'));

			container.appendChild(labelContainer);

			container.addEventListener('click', e => {
				container.style.display = 'none';
				e.stopPropagation();
			});

			document.body.appendChild(container);

			return container;
		}

		function getOrCreatePreviewWindowEl() {
			return document.querySelector('.zakeke-cart-preview-window')
				|| createPreviewWindowEl();
		}

		function handlePreview(previewEl) {
			previewEl.addEventListener('click', () => {
				const previewWindowEl                           = getOrCreatePreviewWindowEl();
				previewWindowEl.querySelector('img').src        = previewEl.dataset.url;
				previewWindowEl.querySelector('h3').textContent = previewEl.dataset.label;
				previewWindowEl.style.display                   = 'flex';
			});
		}

		function handleSliding(previewsEl) {
			new Glide(previewsEl, {
				perView: 2
			}).mount();

			if (previewsEl.querySelectorAll('li').length <= 2) {
				previewsEl.querySelector('div[data-glide-el="controls"]').style.display = 'none';
			}

			previewsEl.querySelectorAll('.zakeke-cart-preview').forEach(handlePreview);
		}

		if (!window.Glide) {
			return;
		}

		document.querySelectorAll('.zakeke-cart-previews').forEach(handleSliding);
		setInterval(() => {
			Array.from(document.querySelectorAll('.zakeke-cart-previews')).filter(el => !el.classList.contains('glide--slider')).forEach(handleSliding);
		}, 500);
	}

	function handleAjaxAddToCart() {
		document.querySelectorAll('.ajax_add_to_cart.product-type-zakeke').forEach(element => {
			element.addEventListener('click', e => e.stopPropagation());
		});
	}

	function handleProductQuicklook() {
		if (document.querySelector('.single-product')) {
			return;
		}

		let handledFormList = [];

		setInterval(() => {
			const forms = Array.from(document.querySelectorAll('form.cart'));

			forms
				.filter(form => !handledFormList.includes(form))
				.filter(form => {
					const zakekeInput = form.querySelector('input[name=zdesign]');
					return zakekeInput && zakekeInput.value === 'new';
				})
				.forEach(form => {
					handledFormList.push(form);

					form.addEventListener('submit', (e) => {
						e.stopPropagation();
					});
				});
		}, 500);
	}

	handlePreviews();
	handleAjaxAddToCart();
	handleProductQuicklook();
}

if (document.readyState === 'complete'
	|| document.readyState === 'loaded'
	|| document.readyState === 'interactive') {
	zakekeProductPage();
} else {
	document.addEventListener('DOMContentLoaded', zakekeProductPage);
}
