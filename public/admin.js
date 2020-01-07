




var help = new function()
    {
        this.on = false;
        this.speed = 500;
        var self = this;
    
    this.init = function()
    {
        if($('#help #help_inner').css('display') == "block")
        {
            self.on = true;
        }
    };
    
    this.continueToggle = function()
    {
        if(!self.on)
        {
            $('#help #help_inner').slideDown(self.speed, self.contentIn);
        }
        else
        {
            self.contentOut();
        }
    };
    
    this.toggle = function()
    {
        $.ajax({
            url: "//"+WEB_ROOT+PUBLIC_DIR+"/admin/toggle_help",
            timeout: 5000,
            type: 'GET',
            dataType: 'json',
            success: function(data, textStatus){
            self.continueToggle();
            },
            error: function(x, txt, e){
            alert(txt);
            }
        });
    };
    
    this.contentIn = function()
    {
        $('#help #help_inner div').fadeTo(self.speed/3, 1, function(){
            self.on = true;
        });
    };
            
    
    this.contentOut = function()
    {
        $('#help #help_inner div').fadeTo(self.speed/3, 0, self.hideHelp);
        
    };
    
    this.hideHelp = function()
    {
        $('#help #help_inner').slideUp(self.speed, function(){
            self.on = false;
        });
    };         
    };


var toggle = function(link)
{              
    var item = link.parent();
    var img = item.find('> i');
    link.empty();       
    var list = item.find('> ul');
    if (list.css('display') === 'none') {
        list.removeClass();
        //list.show(200);
        link.append('-');
        if (!/file/.test(img.attr('class'))) {
            img.attr('class', 'far fa-folder-open');
        }
    }
    else {
        list.addClass('hidden_sections');
        //list.hide(200);
        link.append('+');
        if (!/file/.test(img.attr('class'))) {
            img.attr('class', 'far fa-folder');
        }
    }
};


var properties = function()
{
    /*
      $('#properties fieldset legend').bind('click', function(e){
      e.preventDefault();
      var $this = $(this);
      toggle_p($this);
      });
    */
    
    $('#image_sizes form span.edit_box').bind('click', function(e){
        var $this = $(this);
        if(edit_box.locked == 0)
        {
            var id_arr = $this.attr('id').split('_');
            var id = id_arr[1];
            var field = id_arr[0];
            edit_box.init($this.parent(), $this.text(), id, field);
            edit_box.enter();
        }
    });
};


var edit_box = new function()
    {
    this.current_text = '';
    this.old_text = '';
    this.parent_element;
    this.locked = 0;
    this.id = 0;
    this.field = '';
    
    var self = this;

    this.init = function(p, t, id, field)
    {
        self.parent_element = p;
        self.current_text = t;
        self.old_text = self.current_text;      
        self.id = id;
        self.field = field;
    };

    this.error = function(msg)
    {
        alert(msg);
        self.current_text = self.old_text;
        self.leave();
    };

    this.enter = function()
    {
        self.locked = 1;
        self.parent_element.empty().append('<input type="text" id="'+self.field+'_'+self.id+'" value="'+self.current_text+'" />');          
        var input = self.parent_element.find('input');
        input.focus();
        input.bind('blur', function(e){
            var $this = $(this);
            self.current_text = $this.val();
            
            if(self.current_text == self.old_text)
            {
                self.leave();
            }
            else
            {
                $.ajax({
                    url: window.location.toString(),
                    timeout: 5000,
                    type: 'POST',
                    dataType: 'json',
                    data: 'field='+self.field+'&id='+self.id+'&value='+self.current_text,
                    success: function(data, textStatus){            
                    if(data == 1)
                        {
                        self.error('server error');
                        }
                    else if(data == 2)
                        {
                        self.error('invalid option value');
                        }
                    else
                        {
                        self.leave();
                        }                                                               
                    },
                    error: function(x, txt, e){
                    self.error(txt);                
                    }
                });         
            }
        });
        
    };

    this.leave = function()
    {
        self.locked = 0;
        self.parent_element.empty().append('<span class="option" id="'+self.field+'_'+self.id+'">'+self.current_text+'</span>');            
        var span = self.parent_element.find('span');        
        span.bind('click', function(e){
            var $this = $(this);
            if(self.locked == 0)
            {
                self.init($this.parent(), $this.text(), $this.attr('id').split('_')[1]);
                self.enter();
            }
        });
    };
    };



var tree = function()
{
    $('ul#tree li a.toggle, ul#tree ul li a.toggle').bind('click', function(e){
        e.preventDefault();
        var $this = $(this);
        toggle($this);
    });


    $('ul#tree, ul#tree ul').sortable({
        placeholder: "highlight",
        axis: 'y',
        update: function (event, ui) {
            var data = $(this).sortable('serialize');

            console.log(data);

            $.ajax({
                data: data,
                type: 'POST',
                url: "//"+WEB_ROOT+PUBLIC_DIR+"/admin/dsection/sort"
            })
            .done(function(data){
                //console.log(data);
            });
        }
    });
};


    
var radios = function()
{
    $(".radios input[name='data_type']").change(function(){
        if($('.radios input')[4].checked)
        {
            $('#containers').removeClass('hidden');
        }
        else
        {
            $('#containers').addClass('hidden');
        }
    });     
};








$(document).ready(function(){



    $('#data p').bind("click", function(){

      
        var $this = $(this);
        var data = $this.parent().find('pre');
        if(data.css('display') == 'none') {

            $('#data .item pre').hide();
            data.show();

        } else {
            data.hide();
        }
        return false;
    });


    if($('ul#tree').length > 0)
        {            
        tree();
        }

    if($('.radios').length > 0)
        {
        radios();
        }

    if($('#image_sizes').length > 0)
        {
        properties();
        }


    $('a.confirm').bind("click", function(e){
        return confirm('Are you sure you want to do this?');
        });
    
    $('form.confirm').submit(function(){
        return confirm('Are you sure you want to do this?');
        });



    if($('#help').length > 0)
            {
        help.init();
                $('#help_tab').bind("click", function(e){
                        e.preventDefault();
            help.toggle();
                    });
            }

    if($('textarea.raw').length < 1)
    {
        tinymce.init({
            selector: 'textarea',
            convert_urls: false,
            theme: "silver",
           
            paste_remove_styles: true,
            paste_preprocess: function(pl, o) {
                // Content string containing the HTML from the clipboard
                //alert(o.content);
                o.content = o.content.replace(/(<([^>]+)>)/gi, '');
            },
            external_plugins: {
                blogImages: '//' + WEB_ROOT + PUBLIC_DIR + '/vendor/js/blogImages.js'
            },
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help'
            ],
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | blogimage'
        });
    }
   

    // new - disable buttons
    $('body').on('click', 'a.disabled', function(event) {
        event.preventDefault();
    });

});
