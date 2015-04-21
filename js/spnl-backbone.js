
(function($) {
 
    var AppRouter = Backbone.Router.extend({
        routes: {
            "spnl/:action/:id": "defaultRoute" // matches http://example.com/#anything-here
        }
    });


    var x =  Backbone.View.extend({
        tagName: 'div',
        // Get the template from the DOM
        template : wp.template( 'my-awesome-template'),
 
        // When a model is saved, return the button to the disabled state
        initialize:function () {
            var _this = this;
            
        }
        ,render: function() {
        this.$el.html(this.template({name: 'world'}));
    }
 
       
    });


    // Initiate the router
    var app_router = new AppRouter;

    app_router.on('route:defaultRoute', function(actions,id) {
        alert(actions + ' ' + id);
        $(document).ready(function(){
        var t = new x({});
        t.render();
        $('body').append(t.$el);
        //console.log(t.$el);
    });
 

    });

    // Start Backbone history a necessary step for bookmarkable URL's
    Backbone.history.start();





    /** Our code here **/
 
}(jQuery));