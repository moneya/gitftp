define([
    'text!pages/settings/projects.html'
], function (page) {
    d = Backbone.View.extend({
        events: {},
        render: function () {
            var that = this;
            this.$el.html(this.$e = $('<div class="bb-loading side-anim">').addClass(viewClass()));
            this.template = _.template(page);
            that.$e.html(that.template());
            setTitle('Projects | Settings');
        }
    });
    return d;
});