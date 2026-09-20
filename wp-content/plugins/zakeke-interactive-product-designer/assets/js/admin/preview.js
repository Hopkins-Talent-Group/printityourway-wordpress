jQuery(document).ready(function () {
	jQuery('.zakeke-preview').click(function (event) {
		tb_show(this.lastElementChild.title, this.lastElementChild.src);
		event.stopPropagation();
	});
});
