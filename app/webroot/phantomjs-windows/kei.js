var page = require('webpage').create(),
system = require('system'),
address, output, size, orientation;
orientation = 'portrait';

address = system.args[1];
output = system.args[2];
page.viewportSize = { width: 600, height: 600 };
if(system.args.length>4 && system.args[4]!=''){
    orientation = system.args[4];
    if(system.args[4]!='landscape')
        orientation = 'portrait';
}
if (system.args.length > 3 && system.args[2].substr(-4) === ".pdf") {
    var footerString;
    if( system.args[5] != undefined &&  system.args[5] != "" ) {
        footerString = system.args[5];
    } else {
        footerString = '<li class="footer_li">3145 - 5th Ave NE, Calgary, Alberta, Canada T2A 6K4 </li>' +
                '<li class="footer_li">* Tel: 403.614.6113</li>' +
                '<li class="footer_li">* Web: <a href="banhmisub.com">banhmisub.com</a></li>';
    }
    footerString = '<div class="footer">' +
                        '<ul class="footer_ul" style="">' +
                            footerString +
                            '<li style="list-style: none; float: right;">Page [PAGE_NUM]</li>' +
                        '</ul>' +
                    '</div>';
    size = system.args[3].split('*');
    page.paperSize = size.length === 2 ? {  width: size[0], height: size[1], margin: '0.8cm'}
                                       : {  format: system.args[3],
                                            orientation: orientation,
                                            margin: {
                                                // top: '1in',
                                                top: '0.8cm',
                                                bottom: '0.8cm',
                                                left: '0.8cm',
                                                right: '0.8cm',
                                            },
                                            width:  '8.5in',
                                            height: '11in',
                                            headerHeight : "25px",
                                            footer: {
                                                height: "1cm",
                                                contents: phantom.callback(function(pageNum, numPages) {
                                                    if(pageNum == 2) {
                                                        page.evaluate(function() {
                                                            return $('thead').append('<tr style="height:1px">');
                                                        });
                                                    }
                                                    if( pageNum == 1 ) {
                                                        footerString = replaceCssWithComputedStyle(footerString);
                                                    }
                                                    return footerString.replace("[PAGE_NUM]", pageNum);
                                                })
                                            },
                                        };
}
page.open(address, function (status) {
    if (status !== 'success') {
        console.log('Unable to load the address!');
        phantom.exit();
    } else {
        window.setTimeout(function () {
            page.injectJs('../default/js/jquery-1.10.2.min.js');
            page.render(output);
            console.log('ok');
            phantom.exit();
        }, 200);
    }
});

function replaceCssWithComputedStyle(html) {
  return page.evaluate(function(html) {
    var host = document.createElement('div');
    host.setAttribute('style', 'display:none;'); // Silly hack, or PhantomJS will 'blank' the main document for some reason
    host.innerHTML = html;

    // Append to get styling of parent page
    document.body.appendChild(host);

    var elements = host.getElementsByTagName('*');
    // Iterate in reverse order (depth first) so that styles do not impact eachother
    for (var i = elements.length - 1; i >= 0; i--) {
      elements[i].setAttribute('style', window.getComputedStyle(elements[i], null).cssText);
    }

    // Remove from parent page again, so we're clean
    document.body.removeChild(host);
    return host.innerHTML;
  }, html);
}
