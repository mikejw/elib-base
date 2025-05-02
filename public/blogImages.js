

tinymce.PluginManager.add('blogImages', function(editor, url) {

  var id = $('form').data('id');

  var openDialog = function () {
    function submit(event) {

      if (event.origin !== window.origin  || (!event.data.mceAction || !event.data.content)) {
        return;
      } else {
        switch (event.data.mceAction) {
          case 'insertContent':
            editor.insertContent(event.data.content);
            editor.setContent(editor.getContent().replace(/\[blog-image\:([A-Za-z0-9=]*)\]/g, function (match, match1) {
              var data = JSON.parse(atob(match1));
              return '<img data-payload="' + match1 + '" class="' + data.centered + ' ' + data.fluid + '" src="/uploads/' + data.size + data.filename + '" alt="">';
            }));
            api.close();
            window.removeEventListener('message', submit);
            break;
          default:
            break;
        }

      }
    }
    window.addEventListener('message', submit);

    var api = editor.windowManager.openUrl({
      url: '/admin/blog/blog_images/' + id,
      title: 'Blog Images',
      onCancel: function() {
        window.removeEventListener('message', submit);
      }
    });
    return api;
  };

  
  if (!!id) {
    editor.ui.registry.addButton('blogimage', {
      text: 'Blog Image',
      onAction: function () {
        openDialog();
      }
    });  
  }

  return {
    getMetadata: function () {
      return {
        name: 'blogimage',
        url: 'https://empathy.sh/docs/elib-cms/README.md'
      };
    }
  };
});
