

tinymce.PluginManager.add('blogImages', function(editor, url) {
  var openDialog = function () {
    var id = $('form').data('id');

    function submit(event) {
      if (event.origin !== window.origin) {
        return;
      } else {
        switch (event.data.mceAction) {
          case 'insertContent':
            editor.insertContent(event.data.content);
            editor.setContent(editor.getContent().replace(/\[blog-image\:([A-Za-z0-9=]*)\]/g, function (match, match1) {
              var data = JSON.parse(atob(match1));
              return '<img data-payload="' + match1 + '" class="' + data.centered + ' ' + data.fluid + '" src="//' + WEB_ROOT + PUBLIC_DIR + '/uploads/' + data.size + data.filename + '" alt="" />';
            }));
            api.close();
            break;
          default:
            break;
        }
      }
    }
    window.addEventListener('message', submit, { once: true });

    var api = editor.windowManager.openUrl({
      url: '//' + WEB_ROOT + PUBLIC_DIR + '/admin/blog/blog_images/' + id,
      title: 'Blog Images',
      onCancel: function() {
        window.removeEventListener('message', submit);
      }
    });
    return api;
  };
  /* Add a button that opens a window */
  editor.ui.registry.addButton('blogimage', {
    text: 'Blog Image',
    onAction: function () {
      /* Open window */
      openDialog();
    }
  });
  // /* Adds a menu item, which can then be included in any menu via the menu/menubar configuration */
  editor.ui.registry.addMenuItem('example', {
    text: 'Example plugin',
    onAction: function() {
      /* Open window */
      openDialog();
    }
  });
  /* Return the metadata for the help plugin */
  return {
    getMetadata: function () {
      return {
        name: 'Example plugin',
        url: 'http://exampleplugindocsurl.com'
      };
    }
  };
});
