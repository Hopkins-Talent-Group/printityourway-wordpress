(() => {
	function addTempPrefixToInput(name) {
		const prefix = 'ztmp_prefix_';
		return name.includes(prefix) ? name : prefix + name;
	}

	function handleDuplicatedZakekeInput(cart) {
		Array.from(cart.querySelectorAll('input[name=zdesign]')).slice(1).forEach(el => el.remove());
	}

	function zakekeProductPage() {
		const elements = [
			['input[name=zdesign]', '.zakeke-customize-button'],
			['input[name=zconfiguration]', '.zakeke-configurator-customize-button']
		];

		document.querySelectorAll('form.cart, #wholesale_form, .c-variation__form').forEach(cart => {
			const cartSubmit           = cart.querySelector('button[type=submit], .single_add_to_cart_button');
			elements.forEach(element => {
				const zakekeInput      = cart.querySelector(element[0]);
				const customizeElement = cart.querySelector(element[1]);

				if (!zakekeInput) {
					return;
				}

				if (customizeElement) {
					customizeElement.addEventListener('click', e => {
						e.preventDefault();

						if (!cartSubmit.classList.contains('disabled')) {
							handleDuplicatedZakekeInput(cart);

							zakekeInput.value = 'new';

							cartSubmit.addEventListener('click', e =>
								e.stopPropagation()
							);
						}

						if (cartSubmit.tagName === 'A') {
							cart.submit();
						} else {
							cartSubmit.click();
						}
					});
				} else if (cartSubmit) {
					cartSubmit.addEventListener('click', e => {
						if (cartSubmit.classList.contains('disabled')) {
							return;
						}

						e.stopPropagation();
						if (cartSubmit.tagName === 'A') {
							cart.submit();
						}
					});
				}

				cart.addEventListener('submit', e => {
					if (zakekeInput.value !== 'new' || cart.querySelector('.ppom-wrapper')) {
						return;
					}

					e.stopPropagation();
					e.stopImmediatePropagation();

					document.querySelectorAll('*[name=add-to-cart], *[name=add-variations-to-cart]').forEach(input => {
						input.name = addTempPrefixToInput(input.name);
					});
				});
			});
		});
	}

	if (document.readyState === 'complete'
		|| document.readyState === 'loaded'
		|| document.readyState === 'interactive') {
		zakekeProductPage();
	} else {
		document.addEventListener('DOMContentLoaded', zakekeProductPage);
	}
})();
