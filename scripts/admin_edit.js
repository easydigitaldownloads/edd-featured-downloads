function quickEditDownload() {
    var $ = jQuery;
    var _edit = inlineEditPost.edit;
    inlineEditPost.edit = function(id) {
        var args = [].slice.call(arguments);
        _edit.apply(this, args);

        if (typeof(id) == 'object') {
            id = this.getId(id);
        }
      //  if (this.type == 'post') {
            var
            // editRow is the quick-edit row, containing the inputs that need to be updated
            editRow = $('#edit-' + id),
            // postRow is the row shown when a book isn't being edited, which also holds the existing values.
            postRow = $('#post-'+id),

            // get the existing values
            // the class ".column-featured" is set in display_custom_quickedit_book

            edd_feature_download = !! $('.column-featured>*', postRow).attr('checked');

            // set the values in the quick-editor
           // $(':input[name="book_author"]', editRow).val(author);
            $(':input[name="edd_feature_download"]', editRow).attr('checked', edd_feature_download);
     //   }
    };
}
// Another way of ensuring inlineEditPost.edit isn't patched until it's defined
if (inlineEditPost) {
    quickEditDownload();
} else {
    jQuery(quickEditDownload);
}