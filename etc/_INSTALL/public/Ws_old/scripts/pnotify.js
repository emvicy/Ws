
//--------------------------------------------------------------
// PNotify
var stack_topright = {"dir1": "down", "dir2": "left", "push": "top"};
var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};
var stack_bottomright = {"dir1": "up", "dir2": "up", "push": "top"};
var stack_bottomleft = {"dir1": "up", "dir2": "up", "push": "top"};
var stack_modal = {"dir1": "down", "dir2": "right", "push": "top", "modal": true, "overlay_close": true};
var stack_bar_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0};
var stack_bar_bottom = {"dir1": "up", "dir2": "right", "spacing1": 0, "spacing2": 0};
var stack_context = {"dir1": "down", "dir2": "left", "context": $("#stack-context")};

// Process Data
function WsProcess(sData) {

    // example
    // oJson.data {"receiver":"PNnotify","PNotifyType":"notice","user":"robot","message":"Foo\\Controller\\Ws\\Push::test\n**PUSH TEST** at 2025-03-28 08:21:14\npid: 140506\n","datetime":"2025-03-28 08:21:14"}
    var oJson = JSON.parse(sData);

    var sClass = "stack-topright";
    var oStack = stack_topright;

    if ('info' === oJson.data.PNotifyType) {
        var sClass = "stack-topleft";
        var oStack = stack_topleft;
    }
    if ('success' === oJson.data.PNotifyType) {
        var sClass = "stack-topright";
        var oStack = stack_topright;
    }
    if ('notice' === oJson.data.PNotifyType) {
        var sClass = "stack-bottomright";
        var oStack = stack_bottomright;
    }
    if ('error' === oJson.data.PNotifyType) {
        var sClass = "stack-bottomleft";
        var oStack = stack_bottomleft;
    }

    // PNotify.desktop.permission();
    new PNotify({
        title: oJson.data.PNotifyType,
        text: oJson.data.message,
        addclass: sClass,
        stack: oStack,
        type: oJson.data.PNotifyType,
        textTrusted: true,
        desktop: {
            desktop: true,
            title: oJson.data.PNotifyType,
            icon: '/Ws_old/assets/' + oJson.data.PNotifyType + '.png',
            text: aText[1].replace(/<\/?[^>]+(>|$)/g, "")
        }
    });
}