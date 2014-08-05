/*globals casper:false */
[
    '/index.html',
    '/rss.xml',
    '/sitemap.xml',
    '/css/style.css',
    '/robots.txt',
    '/js/all.js',
].forEach(
    function (page) {
        casper.test.begin(
            page + ' page can be rendered',
            function (test) {
                casper.start(
                    'http://localhost:4000' + page,
                    function () {
                        test.assertHttpStatus(200);
                    }
                );
                casper.run(
                    function () {
                        test.done();
                    }
                );
            }
        );
    }
);
