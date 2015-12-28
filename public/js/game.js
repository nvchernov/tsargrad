/**
 * Created by rudnev on 25.12.15.
 */

(function () {
    window.player = {}; // Игрок.
    window.enemy = {}; // Вражеский игрок.

    window.Models = {}; // Все модели.
    window.Views = {}; // Все представления

    var Resource = window.Models.Resource = Backbone.Model.extend({idAttribute: 'name'});
    window.Models.Resources = Backbone.Collection.extend({model: Resource});

    var Squad = window.Models.Squad = Backbone.Model.extend({});
    window.Models.Squads = Backbone.Collection.extend({model: Squad});

    window.Models.Army = Backbone.Model.extend({});
    window.Models.Castle = Backbone.Model.extend({});

    // Ресурсы по умолчанию...
    window.defaultResources = [{name: 'gold', count: 0}, {name: 'wood', count: 0}, {name: 'food', count: 0}];

    /////// Представления

    // Ресурсы...
    var ResourceLi = window.Views.ResourceLi = Marionette.ItemView.extend({
        tagName: 'li',
        bindings: {
            '.res-name': {
                attributes: [{
                    name: 'class',
                    observe: 'name',
                    onGet: function (val) {
                        return val == 'gold' ? 'text-warning' : val == 'food' ? 'text-info' : 'text-success'
                    }
                }],
                observe: 'name',
                onGet: function (val) {
                    return val == 'gold' ? 'Золото' : val == 'food' ? 'Еда' : 'Дерево';
                }
            },
            '.res-count': 'count'
        },
        template: '#t-game-nav-res-li',
        onRender: function () {
            this.stickit();
        }
    });
    window.Views.ResourcesNav = Marionette.CollectionView.extend({
        tagName: 'ul',
        className: 'nav navbar-nav resources-nav',
        childView: ResourceLi
    });

    var ResourceView = window.Views.Resource = Marionette.ItemView.extend({
        tagName: 'li',
        className: 'list-group-item',
        template: '#t-game-res',
        bindings: {
            ':el': {
                attributes: [{
                    name: 'class',
                    observe: 'name',
                    onGet: function (val) {
                        return val == 'gold' ? 'list-group-item-warning' : val == 'food' ? 'list-group-item-info' : 'list-group-item-success'
                    }
                }]
            },
            '.res-name': {
                observe: 'name',
                onGet: function (val) {
                    return val == 'gold' ? 'Золото' : val == 'food' ? 'Еда' : 'Дерево';
                }
            },
            '.res-count': 'count'
        },
        onRender: function () {
            this.stickit();
        }
    });
    window.Views.Resources = Marionette.CollectionView.extend({
        tagName: 'ul',
        className: 'list-group resources',
        childView: ResourceView,
        emptyView: new Marionette.ItemView.extend({
            template: 'Нет ресурсов'
        })
    });
    // ... Ресурсы.

    // Отряды...
    var SquadView = window.Views.Squad = Marionette.ItemView.extend({
        tagName: 'tr',
        template: '#t-game-squad',
        bindings: {
            '#squad-name': 'name',
            '#squad-size': 'size',
            '#squad-date-begin': 'crusade_at',
            '#squad-date-battle': 'battle_at',
            '#squad-date-end': 'crusade_end_at'
        },
        onRender: function () {
            this.stickit();
        }
    });
    var SquadsView = window.Views.Squads = Marionette.CompositeView.extend({
        tagName: 'table',
        className: 'table table-condensed',
        template: '#t-game-squads',
        childView: SquadView,
        attachHtml: function (collectionView, childView, index) {
            collectionView.$('tbody').append(childView.el);
        },
        emptyView: Marionette.ItemView.extend({
            tagName: 'tr',
            template: _.template('<td colspan="5" class="text-center">Нет отрядов</td>')
        })
    });
    // ...Отряды.

    // Армия...
    window.Views.ArmyLi = Marionette.ItemView.extend({
        tagName: 'li',
        bindings: {
            '.army-size': 'size'
        },
        template: '#t-game-nav-army-li',
        onRender: function () {
            this.stickit();
        }
    });
    window.Views.Army = Marionette.LayoutView.extend({
        tagName: 'div',
        className: 'modal-dialog',
        template: '#t-game-army',
        attributes: {
            role: 'document'
        },
        ui: {
            buyCost: '#m-army-cost',
            buySize: '#my-army-buy-size',
            squadsSize: '#my-army-sizesquads',
            resultBuy: '#m-army-result-buy',
            resultUpgrade: '#m-army-result-upgrade'
        },
        regions: {
            squadsRegion: '#my-squads'
        },
        events: {
            'click #m-army-buy': function () {
                var ui = this.ui;

                $.post('game/armies/' + this.model.id + '/buy', {count: ui.buySize.slider('getValue')},
                    function (resp) {
                        if (resp.success) {
                            player.army.set(resp.data.army);
                            player.squads.set(resp.data.squads);

                            ui.resultBuy.text('Новые воины успешно наняты!');
                            ui.resultBuy.closest('.form-group').removeClass('has-error has-success').addClass('has-success');
                            ui.resultBuy.closest('.form-group').fadeIn().delay(5000).fadeOut();
                        }
                    }, 'json'
                ).fail(function (xhr) {
                        ui.resultBuy.text(xhr.responseJSON.message);
                        ui.resultBuy.closest('.form-group').removeClass('has-error has-success').addClass('has-error');
                        ui.resultBuy.closest('.form-group').fadeIn().delay(5000).fadeOut();
                    }
                );
            },
            'click #m-army-upgrade': function () {
                var ui = this.ui;

                $.post('game/armies/' + this.model.id + '/upgrade', {},
                    function (resp) {
                        if (resp.success) {
                            player.army.set(resp.data.army);
                            player.squads.set(resp.data.squads);

                            ui.resultUpgrade.text('Армия улучшена!');
                            ui.resultUpgrade.closest('.form-group').removeClass('has-error has-success').addClass('has-success');
                            ui.resultUpgrade.closest('.form-group').fadeIn().delay(5000).fadeOut();
                        }
                    }, 'json'
                ).fail(function (xhr) {
                        ui.resultUpgrade.text(xhr.responseJSON.message);
                        ui.resultUpgrade.closest('.form-group').removeClass('has-error has-success').addClass('has-error');
                        ui.resultUpgrade.closest('.form-group').fadeIn().delay(5000).fadeOut();
                    }
                );
            }
        },
        initialize: function (options) {
            this.mergeOptions(options, ['squads']);
        },
        bindings: {
            '#my-army-level': 'level',
            '#my-army-size': 'size',
            '#my-army-buy-price': {
                observe: 'buyPrice',
                onGet: function (buyPrice) {
                    this.ui.buyCost.text(buyPrice * this.ui.buySize.slider('getValue'));
                    return buyPrice;
                }
            },
            '#my-army-level-up': {
                observe: 'level',
                onGet: function (level) {
                    return 1 + +level;
                }
            },
            '#my-army-upgrade-price': 'upgradePrice'
        },
        getSquadsSize: function () {
            var size = 0;
            this.squads.each(function ($s) {
                size += +$s.get('size');
            });
            return size;
        },
        onRender: function () {
            var self = this, ui = this.ui;

            ui.buySize.slider({tooltip_position: 'bottom'});
            ui.buySize.slider().on('change', function (e) {
                ui.buyCost.text(self.model.get('buyPrice') * e.value.newValue);
            });

            ui.squadsSize.text(this.getSquadsSize());
            this.squads.on('update change:size', function () {
                ui.squadsSize.text(self.getSquadsSize());
            });

            var squadsView = new SquadsView({collection: this.squads});
            this.getRegion('squadsRegion').show(squadsView);

            this.stickit();
        }
    });
    // ...Армия.

    // Вражеский замок...
    window.Views.EnemyCastle = Marionette.LayoutView.extend({
        tagName: 'div',
        className: 'modal-dialog',
        template: '#t-game-enemy-castle',
        regions: {
            resourcesRegion: '#enemy-resources'
        },
        attributes: {
            role: 'document'
        },
        ui: {
            castleName: '#enemy-castle-name',
            size: '#m-squad-size',
            name: '#m-squad-name',
            help: '#m-squad-name-h',
            error: '#m-squad-error-h'
        },
        initialize: function (options) {
            this.mergeOptions(options, ['castle', 'resources']);
        },
        validate: function () {
            var ui = this.ui;
            if (ui.name.val() == '') {
                ui.name.closest('.form-group').addClass('has-error').delay(5000).queue(function () {
                    $(this).removeClass('has-error');
                });
                ui.help.fadeIn().delay(5000).fadeOut();
                return false;
            }
            return true;
        },
        events: {
            'click #m-crusade': function () {
                var self = this, ui = this.ui;

                if (!this.validate()) {
                    return;
                }

                $.post('game/armies/' + this.model.id + '/crusade', {
                        name: ui.name.val(),
                        count: ui.size.slider('getValue'),
                        goal: this.castle.id
                    },
                    function (resp) {
                        $('#enemy-castle-modal').modal('hide');
                        if (resp.success) {
                            var options = {
                                theme: 'bootstrapTheme',
                                closeWith: ['button'],
                                layout: 'bottomRight'
                            }, s = resp.data;
                            options.text = 'Отряд "' + s.name + '" (' + s.size + ' чел.) отправился в поход на вражеский замок ' +
                                '"' + s.goal.name + '"';
                            noty(options);
                        }
                    }, 'json'
                ).fail(function (xhr) {
                        ui.error.text('Ошибка! ' + xhr.responseJSON.message);
                        ui.error.closest('.form-group').fadeIn().delay(5000).fadeOut();
                    }
                );
            }
        },
        bindings: {
            '#army-crusade': {
                classes: {
                    hidden: {
                        observe: 'size',
                        onGet: function (size) {
                            return +size == 0;
                        }
                    }
                }
            },
            '#m-squad-size': {
                observe: 'size',
                onGet: function (size) {
                    // TODO: пофиксить...
                    // cannot call methods on slider prior to initialization; attempted to call 'setAttribute'
                    size = +size;
                    if (!size) { return }
                    var sizer = this.ui.size;
                    var val = +sizer.val() > size ? size : +sizer.val();
                    sizer.slider('setAttribute', 'max', size);
                    sizer.slider('setValue', val);
                    sizer.slider('refresh');
                    return val;
                },
                updateModel: false
            }
        },
        onRender: function () {
            var self = this, ui = this.ui;

            ui.castleName.text(this.castle.get('name'));
            this.listenTo(this.castle, 'change', function () {
                ui.castleName.text(self.castle.get('name'));
            });
            ui.size.slider({tooltip_position: 'bottom'});

            var resourcesView = new Views.Resources({collection: this.resources});
            this.getRegion('resourcesRegion').show(resourcesView);

            this.stickit();
        }
    });
})();

