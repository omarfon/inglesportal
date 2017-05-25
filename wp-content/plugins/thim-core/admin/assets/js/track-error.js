(function (a) {
    a.config('https://81ce8c93b92e4699b4692726126eb187@sentry.io/150519',
        {
            ignoreErrors: [
                'Cannot set property',
                'is not a function',
                'fb_xd_fragment'
            ]
        }).install();
})(Raven);