/**
 * steady_btn_paywall
 */

/*global tinymce:true */

tinymce.PluginManager.add('steady_btn_paywall', function(editor) {

	var paywall_tag_text = '<!--steady-paywall-->';
	var paywall_tag_img = '<img src="' + tinymce.Env.transparentSrc + '" ' + 'class="mce-wp-steady-paywall" title="Steady Paywall" data-mce-resize="false" data-mce-placeholder="1" alt="" />';
	var paywall_tag_img_re = /(?:<img [^>]*class=\"mce-wp-steady-paywall\"[^>]*>)/; 

	function text_tag_exists(content) {
		return /<\!--steady-paywall-->/.test(content);
	}

	function img_tag_exists(content) {
		return paywall_tag_img_re.test(content)
	}

	editor.addCommand('add_paywall_code', function() {
		var content = editor.getContent();
		if (text_tag_exists(content) || img_tag_exists(content)) {
			alert( editor.getLang('steady_wp_translation.paywall_code_exists') );
		}else {
			editor.execCommand('mceInsertContent', false, '<p>' + paywall_tag_text + '</p>');			
		}
	});

	editor.addButton('add_paywall', {
		title: editor.getLang('steady_wp_translation.insert_paywall_code'),
		cmd: 'add_paywall_code',
		image: steady_wp_path + '/admin/img/icon_20.png',
		tooltip: editor.getLang('steady_wp_translation.steady_paywall'),
	});

	// Replace tag with image
	editor.on( 'BeforeSetContent', function( e ) {
		// Fires before contents being set to the editor.
    if ( e.content ) {
      if ( text_tag_exists(e.content) ) {
        e.content = e.content.replace( paywall_tag_text, paywall_tag_img );
      }
    }
	});

	// Replace image with tag
	editor.on( 'PostProcess', function( e ) {
		// 	Fires when the contents in the editor is being serialized.
    if ( e.content ) {
      if (img_tag_exists(e.content)) {
        e.content = e.content.replace( paywall_tag_img_re, paywall_tag_text );
      }
    }
	});

});
