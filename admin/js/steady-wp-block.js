( function( blocks, i18n, element, hooks ) {
	var el = element.createElement;
	var __ = i18n.__;

	var svg_icon = el(
		'svg',
		{
			width: 20,
			height: 20,
			viewBox: "0 0 40 40"
		},
		[
			el('defs', {}, el('clipPath', { id: 'clip0' }, el('rect', { width: 16.4776, height: 24, fill: 'white', transform: 'translate(11.75 8)' }))),
			el('path', {
				d: 'M20 40C31.0457 40 40 31.0457 40 20C40 8.9543 31.0457 0 20 0C8.9543 0 0 8.9543 0 20C0 31.0457 8.9543 40 20 40Z',
				fill: '#291E38'
			}),
			el('g', { 'clip-path': 'url(#clip0)' }, [
				el('path', {
					opacity: '0.5',
					d: 'M19.989 22.174L19.989 22.1766C19.7808 22.2193 19.4684 22.2833 19.0518 22.3686C18.1687 22.5433 17.4849 22.8764 17.0002 23.3678C16.5156 23.8592 16.2733 24.4434 16.2733 25.1204C16.2733 25.3506 16.2972 25.5687 16.3452 25.7749L12.129 27.0699C11.8763 26.3633 11.75 25.6152 11.75 24.8256C11.75 23.1439 12.31 21.6806 13.4301 20.4358C14.5501 19.1909 16.1225 18.3719 18.1472 17.9788C18.9629 17.8582 19.5755 17.7582 19.985 17.6786V17.6777C20.2024 17.6358 20.5284 17.5591 20.9631 17.4476C22.6181 17.124 23.4455 16.2612 23.4455 14.8592C23.4455 14.7301 23.4368 14.6039 23.4194 14.4808C23.4054 14.382 23.3858 14.2812 23.3606 14.1863L27.495 12.9182C27.7398 13.6202 27.8622 14.3791 27.8622 15.1827C27.8622 16.8868 27.3249 18.3104 26.2503 19.4536C25.1757 20.5968 23.7142 21.3625 21.8658 21.7508C21.0328 21.948 20.4072 22.0891 19.989 22.174Z',
					fill: 'white'
				}),
				el('path', {
					d: 'M19.9888 8.00035C22.1444 8.01807 23.987 8.70818 25.5249 10.0707C26.4641 10.9028 27.1203 11.8524 27.4934 12.9195L23.36 14.1877C23.2221 13.67 22.9174 13.209 22.4461 12.8047C21.8048 12.2544 20.9887 11.969 19.9888 11.9485C19.9596 11.9481 19.9252 11.9473 19.8957 11.9473C18.8425 11.9473 17.9828 12.2331 17.3166 12.8047C16.6503 13.3763 16.3172 14.0611 16.3172 14.8592C16.3172 16.2612 17.1446 17.124 18.7996 17.4476C19.328 17.5508 19.724 17.6278 19.9875 17.6785V17.6777C20.397 17.7573 21.0113 17.8577 21.8304 17.9788C23.8551 18.3719 25.4275 19.1909 26.5475 20.4358C27.6676 21.6806 28.2276 23.1439 28.2276 24.8256C28.2276 26.7475 27.4791 28.4237 25.9821 29.8542C24.4851 31.2847 22.5089 32 19.9888 32C17.4687 32 15.4925 31.2847 13.9955 29.8542C13.1137 29.0116 12.4916 28.0837 12.1292 27.0705L16.3454 25.776C16.4751 26.3325 16.7795 26.802 17.2587 27.1843C17.9156 27.7085 18.8257 27.9705 19.9888 27.9705C21.1519 27.9705 22.062 27.7085 22.7189 27.1843C23.3759 26.6601 23.7044 25.9722 23.7044 25.1204C23.7044 24.4434 23.462 23.8592 22.9774 23.3678C22.4928 22.8764 21.8089 22.5433 20.9258 22.3686C20.5094 22.2833 20.1971 22.2193 19.9888 22.1767V22.1779C19.5247 22.0838 18.8274 21.9414 17.8969 21.7507C16.0485 21.3625 14.587 20.5968 13.5124 19.4536C12.4378 18.3104 11.9005 16.8868 11.9005 15.1827C11.9005 13.1552 12.6795 11.4512 14.2378 10.0707C15.796 8.69023 17.6712 8 19.8634 8C19.9039 8 19.9486 8.00017 19.9888 8.00052V8.00035Z',
					fill: 'white'
				})])
		]
	);

	// remove block from not posts
	hooks.addFilter( 'blocks.registerBlockType', 'steady-wp/paywall', function( settings, name ) {
		var exclude_blocks = [
			'steady-wp/paywall',
		];

		if ( exclude_blocks.indexOf( name ) !== -1 && typenow !== 'post' ) {
			return Object.assign({}, settings, {
				supports: Object.assign( {}, settings.supports, { inserter: false })
			});
		}
		return settings;
	});

	blocks.registerBlockType( 'steady-wp/paywall', {
		title: __( 'Steady Paywall', 'steady-wp' ),
		icon: svg_icon,
		category: 'layout',
		name: 'steady-wp/paywall',
		keywords: [ __( 'steady', 'steady-wp' ), __( 'paywall', 'steady-wp' ) ],
		supports: {
			customClassName: false,
			className: false,
			html: false,
			reusable: false,
			multiple: false,
		},
		edit: function() {
			return el(
				'div',
				{ className: 'wp-block-nextpage wp-block-steady-paywall' },
				el(
					'span',
					null,
					__( 'Steady Paywall', 'steady-wp' )
				)
			);
		},
		save: function() {
			return el(
				'p',
				null,
				el(
					element.RawHTML,
					null,
					'<!--steady-paywall-->'
				)
			);
		},
		transforms: {
			from: [
				{
					type: 'raw',
					schema: {
						'wp-block': { attributes: [ 'data-block' ] },
					},
					isMatch: function(node) {
						return node.dataset && node.dataset.block === 'steady-wp/paywall';
					},
					transform: function() {
						return blocks.createBlock('steady-wp/paywall', {});
					}
				}
			]
		}
	} );
}(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element,
	window.wp.hooks
) );
