<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Tap And Print</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style type="text/css">
        .clicked {
            border: 4px solid deepskyblue;
        }
        .printed {
            -webkit-filter: grayscale(100%); /* Chrome, Safari, Opera */
            filter: grayscale(100%);
        }

        .thumb {
            position: relative;
            width: 200px;
            height: 200px;
            overflow: hidden;
        }

        .thumb img {
            position: absolute;
            left: 50%;
            top: 50%;
            height: 100%;
            width: auto;
            -webkit-transform: translate(-50%,-50%);
            -ms-transform: translate(-50%,-50%);
            transform: translate(-50%,-50%);
        }
        .thumb img.portrait {
            width: 100%;
            height: auto;
        }

        .navbar-brand {
            height: 70px;
        }

    </style>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                        <img alt="Brand" height="50px" src="http://tapandprintid.com/assets/images/logotap.png">
                    </a>
                </div>
                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </nav>

        <div class="container">
            <imagelist :clicked_images="clicked_images" :images="images"></imagelist>
            <button @click="refresh()" type="submit" class="btn btn-primary">Refresh</button>
            <button @click="loadMore()" type="submit" class="btn btn-primary">Load More</button>
        </div>
    </div>
    <template id="imagelist-template">
        <div class="row">
            <div v-for="image in images" :class="{'thumb': true, 'col-lg-3': true, 'col-md-4': true, 'col-xs-6': true }" style="margin:30px;">
                <img @click="pickImage(image)" :src="image.url" :class="{'clicked': image.clicked, 'printed': image.printed }">
            </div>
        </div>
    </template>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/0.9.3/vue-resource.min.js"></script>

    <script>

        new Vue({
            el: '#app',
            data: {
                images: [],
                clicked_images: []
            },
            methods: {
                refresh: function () {
                    alert('refresh');
                },
                loadMore: function () {
                    var self = this;

                    Vue.http.get('/search?q=%23image').then(
                    function (response) {
                        self.images = self.images.concat(response.data);
                        console.log(self.images);
                    },
                    function (response) {
                        console.log('Cannot load images');
                    });
                }
            },
            components: {
                imagelist: {
                    template: '#imagelist-template',
                    props: ['images', 'clicked_images'],
                    methods: {
                        pickImage : function (image) {
                            let i = this.clicked_images.indexOf(image);

                            image.clicked = !image.clicked;

                            if (i == -1) {
                                this.clicked_images.push(image);
                            } else {
                                this.clicked_images.splice(i, 1);
                            }

                            console.log(this.clicked_images);
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>