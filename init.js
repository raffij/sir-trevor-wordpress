jQuery(document).ready(function(){

  /*
  Simple Image Block Override
  */

  SirTrevor.Blocks.Image = SirTrevor.Block.extend({

    type: "Image",
    
    droppable: false,
    uploadable: true,
    
    icon_name: 'image',
    
    loadData: function(data){
      // Create our image tag
      this.$editor.html(jQuery('<img>', { src: data.file.url }));
    },

    onUploadComplete: function(props, attachment){
      data = { 
        file: {
          url: attachment.url,
          id: attachment.id,
          width: attachment.width,
          height: attachment.height,
          filename: attachment.filename
        }
      };
      this.$inputs.hide();
      this.$editor.html(jQuery('<img>', { src: attachment.url })).show();
      this.setData(data);
      this.ready();
    },

    onUploadButtonClicked: function(ev){ 
      ev.preventDefault(); 

      var button = jQuery(ev.currentTarget);
      wp.media.editor.send.attachment = _.bind(this.onUploadComplete, this);
      wp.media.editor.open(button);
    },
    
    onBlockRender: function(){
      /* Setup the upload button */
      var _editor = this;
      this.$inputs.find('input').hide();
      this.$inputs.find('button').bind('click', _.bind(this.onUploadButtonClicked, this));
    }
  });

  SirTrevor.DEBUG = false;

  SirTrevor.setBlockOptions("Tweet", {
    fetchUrl: function(tweetID) {
      return ajaxurl+"?action=sir_trevor_js_twitter_fetch&id="+tweetID;
    }
  });

  jQuery('.acf_postbox .field_type-wysiwyg textarea, #content.wp-editor-area').each( function() {
    new SirTrevor.Editor({
      el: jQuery(this)
    });
  })

});