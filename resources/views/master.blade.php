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
                <div class="navbar-form navbar-left">
                    <input @keyup.enter="performSearch()" v-model="keywords" type="text" class="form-control" placeholder="Search">
                </div>
                <div class="navbar-form navbar-right">
                    <button @click="printget()" type="button" class="btn btn-primary">Refresh</button>
                    <input size="100" v-model="bg_image" type="text" class="form-control" placeholder="Background Image URL">
                    <button @click="print()" type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;&nbsp; Generate PDF</button>
                </div>
            </div>
        </nav>

        <div v-show="show_generating" class="container alert alert-info text-center" role="alert">
            Generating PDF now
            <img src="gear.gif">
        </div>


        <div class="container">
            <imagelist :clicked_images="clicked_images" :images="images"></imagelist>
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

    <script src="js/pdfkit.min.js"></script>
    <script src="js/blob-stream.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/0.9.3/vue-resource.min.js"></script>
    <script src="https://www.promisejs.org/polyfills/promise-7.0.4.min.js"></script>
    <script>

        new Vue({
            el: '#app',
            data: {
                show_generating: false,
                bg_image: 'https://dl.dropboxusercontent.com/s/wauuv6laqfchhl5/ffffff.jpg?dl=0', //default white
                keywords: '',
                cursor: '',
                images: [],
                clicked_images: []
            },
            methods: {
                performSearch: function() {
                    var self = this;
                    let build_url = '/search?q=%23' + self.keywords;
                    Vue.http.get(build_url).then(
                            function (response) {
                                self.images = response.data['data'];
                                self.cursor = response.data['cursor'];
                            },
                            function (response) {
                                console.log('Cannot load images');
                    });
                },
                refresh: function () {
                    this.show_generating = !this.show_generating;
                },
                loadMore: function () {
                    var self = this;
                    let build_url = '/search?q=%23' + self.keywords + '&cursor=' + self.cursor;
                    Vue.http.get(build_url).then(
                    function (response) {
                        self.images = self.images.concat(response.data['data']);
                        self.cursor = response.data['cursor'];
                        console.log(self.cursor);
                    },
                    function (response) {
                        console.log('Cannot load images');
                    });
                },
                print: function() {

                    var self = this;
                    var urls = [];
                    self.show_generating = true;
                    self.clicked_images.forEach(function (image) {
                        self.images.forEach(function (img) {
                            if (image == img) {
                                img.printed = true; //change image to grayscale
                                img.clicked = false;
                            }
                        });
                        urls.push(image.url);
                    });

                    var bg_image = self.bg_image;
                    //put bg image to first element in urls
                    urls.unshift(bg_image);

                    var reqs = [];
                    urls.forEach(function (myurl) {
                        //add function to be called in promise
                        reqs.push(getUri(myurl));
                    });

                    var saveData = (function () {
                        return function (blob, fileName) {
                            var url = window.URL.createObjectURL(blob);
                            window.open(url);
                            window.URL.revokeObjectURL(url);
                        };
                    }());

                    //call all reqs functions
                    Promise.all(reqs).then(function (results) {
                        var doc = new PDFDocument({
                            size: [288, 432],
                            margins: { // by default, all are 72/inch
                                top: 0,
                                bottom: 0,
                                left: 0,
                                right: 0
                            },
                            layout: 'portrait'
                        });

                        var stream = doc.pipe(blobStream());

                        results.forEach(function (imgUri, index) {
                            if (index == 0) {
                                //index 0 for background image
                            } else if (index == (results.length - 1)) {
                                doc.image(results[0], 0, 0, {width: 288});
                                doc.image(imgUri, 18, 72, {width: 252});

                            } else {
                                doc.image(results[0], 0, 0, {width: 288});
                                doc.image(imgUri, 18, 72, {width: 252});
                                doc.addPage();
                            }
                        });

                        doc.end();
                        stream.on('finish', function () {
                            var blob = stream.toBlob('application/pdf');
                            saveData(blob, 'images.pdf');
                        });
                        self.show_generating = false;
                        self.clicked_images = []; //clear selected images after generating pdf
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

        function getUri(url) {
            return new Promise(function(resolve, reject) {
                var image = new Image();
                image.setAttribute('crossOrigin', 'anonymous');
                image.onload = function () {
                    var canvas = document.createElement('canvas');
                    canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
                    canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

                    canvas.getContext('2d').drawImage(this, 0, 0);
                    // ... or get as Data URI
                    resolve(canvas.toDataURL('image/png'));
                };

                image.onerror = function () {
                    reject(Error('Could\'nt load image'));
                };

                image.src = url;
            });
        }
    </script>

</body>
</html>