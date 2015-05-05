/*
 *  Author: Nicholas Merowsky
 *  Date: 24 Apr 2015
 *  Ascription:
 */

function sessionTest(){
    var uid = '';
    $.ajax({ type: "GET",   
        url: "/dolphin/public/ajax/sessionrequests.php",
        data: { p:"sessionTest" },
        async: false,
        success : function(s)
        {
            uid = s;
        }
    });
    return uid;
}

function sendBasketInfo(id){
    $.ajax({ type: "POST",   
        url: "/dolphin/public/ajax/sessionrequests.php",
        data: { p:"sendBasketInfo", id:id },
        async: false,
        success : function(s)
        {
        }
    });
}

function getBasketInfo() {
    basket = "";
    $.ajax({ type: "GET",   
        url: "/dolphin/public/ajax/sessionrequests.php",
        data: { p:"getBasketInfo" },
        async: false,
        success : function(s)
        {
            basket = s;
        }
    });
    if (basket == "") {
        return undefined;
    }else{
        return basket;
    }
}

function removeBasketInfo(id){
    $.ajax({ type: "POST",   
        url: "/dolphin/public/ajax/sessionrequests.php",
        data: { p:"removeBasketInfo", id:id },
        async: false,
        success : function(s)
        { 
        }
    });
}

function flushBasketInfo() {
    $.ajax({ type: "POST",   
        url: "/dolphin/public/ajax/sessionrequests.php",
        data: { p:"flushBasketInfo"},
        async: false,
        success : function(s)
        {
        }
    });
}