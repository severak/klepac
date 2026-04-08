<?=render('_header', ['title'=>$title ?? null]); ?>
<div id="barapp" >

        <!-- nejdříve se přihlásíme přes https://cozmo.github.io/jsQR/ -->

    <div class="message is-success" v-show="success && !name">
        <div class="message-body">
            Nákup proběhl úspěšně. Můžete zadat další.
        </div>
    </div>

        <div class="field has-addons" v-show="!name">
            <div class="control is-expanded">
                <input name="searchFor" class="input" placeholder="číslo karty" v-model="card_id" @change="findUser" id="qrcode">
            </div>
            <div class="control">
                <input type="submit" class="button is-info" value="přihlásit" @click="findUser">
            </div>
        </div>

        <div class="message is-danger" v-show="!name && error">
            <div class="message-body">
                {{ error }}
            </div>
        </div>

        <div v-show="name">
        <div class="message" >
            <div class="message-body">
                <div class="columns is-mobile">
                    <div class="column"><span class="fas fa-user"></span>
                        {{ name }}</div>
                    <div class="column has-text-right">{{ balance }},-</div>
                </div>
            </div>
        </div>

        <div class="message is-danger" v-show="!can_buy_alcohol">
            <div class="message-body">
                Pod 18 let - neprodávat alkohol!
            </div>
        </div>

        <table class="table" v-show="items.length">

            <tr>
                <th>název</th>
                <th>/kus</th>
                <th>počet</th>
                <th>součet</th>
                <th></th>
            </tr>

            <tr v-for="item in items" class="is-vcentered">
                <td><input name="item[]" class="input is-static" placeholder="název zboží" v-model="item.name"></td>
                <td><input name="price[]" inputmode="numeric" class="input is-static" v-model="item.price"></td>
                <td><input name="amount[]" type="number" min="1" max="100" class="input is-static" v-model="item.amount"></td>
                <td class="has-text-right"><div class="input is-static">{{ item.price * item.amount }}</div></td>
                <td><div class="input is-static"><a class="delete" @click="removeItem(item)"></a></div></td>
            </tr>

            <tr>
                <th colspan="3">celkem</th>
                <th v-bind:class="totalClass" class="has-text-right">{{ totalAmount }}</th>
            </tr>

        </table>

        <div class="message is-danger" v-show="error">
            <div class="message-body">
                {{ error }}
            </div>
        </div>

        <div class="columns is-mobile">
            <div class="column">
                <button class="button is-light" @click="addItem">Přidat položku</button>
            </div>
            <div class="column has-text-right">
                <input type="submit" value="Uložit" class="button is-primary" v-show="items.length" @click="completePurchase">
            </div>
        </div>

        <div class="buttons">
        <?php foreach ($items as $item) {
        $warning = $item['price']<0 ? 'has-background-warning' : '';
        ?>
        <button class="button is-fullwidth <?=$warning; ?>" @click="addPredefined" data-id="<?=$item['id']; ?>" data-name="<?=$item['name']; ?>" data-price="<?=$item['price']; ?>"><?=$item['name']; ?></button>
        <?php } ?>
        </div>

        </div>


</div>

<script>

    function sendData(url, data, onsuccess, onerror) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                onsuccess(response);
            } else {
                onerror();
            }
        };
        xhr.send(JSON.stringify(data));
    }

    var app = new Vue({
        el: '#barapp',
        data: {
            card_id: null,
            name: null,
            balance: -1,
            can_buy_alcohol: false,
            items : [],
            error: false,
            success: false
        },

        computed: {
            totalAmount: function()
            {
                var total = 0;
                this.items.forEach(function (item) {
                    total += item.price * item.amount;
                });
                return total;
            },

            totalClass: function () {
                return {
                    'has-text-danger': this.totalAmount > this.balance
                }
            }
        },

        methods: {
            reset: function() {
                this.card_id= null;
                this.name= null;
                this.balance= -1;
                this.can_buy_alcohol= false;
                this.items = [];
                this.error= false;
            },

            findUser: function(ev) {
                var that = this;

                // TODO - z nějakého důvodu se nepropisů QR kódy ze čtečky
                that.card_id = gebi('qrcode').value;

                sendData('/bar/userinfo/', {card_id: that.card_id}, function (response) {
                    if (response.error) {
                        that.error = response.error;
                    } else if (response.name) {
                        that.name = response.name;
                        that.balance = response.balance;
                        that.can_buy_alcohol = response.can_buy_alcohol;
                        that.items = [];
                        that.error = false;
                    }
                }, function () {
                    that.error = 'Došlo k nečekané chybě.';
                });
            },

            addPredefined: function(ev) {
                var name = ev.target.getAttribute('data-name');
                var price = ev.target.getAttribute('data-price');
                var id = ev.target.getAttribute('data-id');
                var alreadyThere = false;
                this.items.forEach(function (item) {
                    if (item.id && item.id==id) {
                        item.amount++;
                        alreadyThere = true;
                    }
                });
                if (alreadyThere) return;

                this.items.push({
                    name: name,
                    price: price,
                    amount: 1,
                    total: price,
                    id: id
                });
            },

            addItem: function(){
                this.items.push({
                    name: "",
                    price: 0,
                    amount: 1,
                    total: 0
                });
            },

            changedItem: function(item) {
                item.total = item.price * item.amount;
            },

            removeItem: function (item) {
                if (item.amount>1) {
                    item.amount -= 1;
                    return;
                }
                this.items.splice(this.items.indexOf(item), 1)
            },

            completePurchase: function () {
                var that = this;

                sendData('/bar/buy/', {card_id: that.card_id, items: that.items}, function (response) {
                    if (response.error) {
                        that.error = response.error;
                        if (response.balance) {
                            that.balance = response.balance;
                        }
                    } else if (response.success) {
                        that.success = true;
                        that.reset();
                    }
                }, function () {
                    that.error = 'Došlo k nečekané chybě.';
                });
            }
        }
    });

    window.app = app;
</script>
<?=render('_footer');?>