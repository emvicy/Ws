//--------------------------------------------------------------
// PNotify

// var stack_topright = {"dir1": "down", "dir2": "left", "push": "top"};
// var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};
// var stack_bottomright = {"dir1": "up", "dir2": "up", "push": "top"};
// var stack_bottomleft = {"dir1": "up", "dir2": "up", "push": "top"};
// var stack_modal = {"dir1": "down", "dir2": "right", "push": "top", "modal": true, "overlay_close": true};
// var stack_bar_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0};
// var stack_bar_bottom = {"dir1": "up", "dir2": "right", "spacing1": 0, "spacing2": 0};
// var stack_context = {"dir1": "down", "dir2": "left", "context": $("#stack-context")};

// Ausgabe Responses
function write(sJson) {

    var oJson = JSON.parse(sJson);
    var aText = oJson.data.split('||');
    var sType = aText[0];
    var sText = aText[1];

    // var oStackDefault = {
    //     dir1: 'down', // up | down
    //     // dir2: 'right', // left | right
    //     firstpos1: 25,
    //     // firstpos2: 25,
    //     modal: false, // true | false
    //     maxOpen: Infinity // int | Infinity
    //     // maxStrategy: 'close',
    //     // maxClosureCausesWait: false
    // };

    var sClass = "stack-topright";

    // object for desktop output
    oContent = {
        title: sType,
        type: sType,
        textTrusted: true,
        // strip_tags
        text: sText,
        // addclass: sClass,
        // @see https://sciactive.com/pnotify/#demos-modules
        modules: new Map([
            ...PNotify.defaultModules,
            [PNotifyDesktop, {
                title: sType,
                text: sText.replace(/<\/?[^>]+(>|$)/g, "")
            }]
        ])
    };

    // // @see https://sciactive.com/pnotify/#stacks
    // if (typeof window.maxOpenClose === 'undefined') {
    //     window.maxOpenClose = new PNotify.Stack(oStackDefault);
    // }

    if ('notice' === sType) {
        // oContent.addclass = "stack-bottomright";
        // oContent.oStack = stack_bottomright;
        PNotify.notice(oContent);
    }
    if ('info' === sType) {
        // oContent.addclass = "stack-topleft";
        // oContent.oStack = stack_topleft;
        PNotify.info(oContent);
    }
    if ('success' === sType) {
        // oContent.addclass = "stack-topright";
        // oContent.oStack = stack_topright;
        PNotify.success(oContent);
    }
    if ('error' === sType) {
        // oContent.addclass = "stack-bottomleft";
        // oContent.oStack = stack_bottomleft;
        // oContent.hide = false;
        PNotify.error(oContent);
    }
}