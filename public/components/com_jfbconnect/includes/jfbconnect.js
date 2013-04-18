/**
 * @package JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @website http://www.sourcecoast.com/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * Ensure global _gaq Google Anlaytics queue has be initialized.
 * @type {Array}
 */
var _gaq = _gaq || [];

var jfbc = {
    base:'',
    return_url:null,
    login:{
        show_modal:false,
        scope:null,
        logout_facebook:false,
        login_started:false, // Prevents multiple concurrent login attempts, mainly caused by the auto-login feature enabled
        // login_custom is used for non XFBML login requests (custom image buttons)
        login_custom:function ()
        {
            FB.login(function (response)
            {
                jfbc.login.on_login();
            }, {
                scope:jfbc.login.scope
            });
        },
        // Action to perform after authentication on FB has occurred
        on_login:function ()
        {
            if (!jfbc.login.login_started)
            {
                jfbc.login.login_started = true;
                FB.getLoginStatus(function (response)
                {
                    if (response.status === 'connected')
                    {
                        // Check that permissions have been granted properly:
                        FB.api('/me/permissions', function (response)
                        {
                        /*    var requiredScope = jfbc.login.scope.split(',');
                            if (requiredScope.length == 0)
                                return; // We always want at least email, so something is wrong in this case*/

                            if (!response || response.error || response.data[0].length == 0)
                                return; // Again, should at least get something as well as the email permission returned

/*                            for (var i = 0; i < requiredScope.length; i++)
                            {
                                var scope = requiredScope[i];
                                if (response.data[0][scope] != 1)
                                    return; // Permission wasn't granted. Need to click
                            }*/
                            // check just email perm as some other perms may be denied by the user, but still should be able to login..
                            if (response.data[0]['email'] != 1)
                                return;

                            if (jfbc.login.show_modal == '1')
                                SqueezeBox.fromElement('jfbcLoginModal', {handler:'adopt', size:{x:500, y:50}});
                            var d = new Date();
                            self.location = jfbc.base + '/index.php?option=com_jfbconnect&task=loginFacebookUser&return=' + jfbc.return_url + '&cb=' + d.getTime();
                        });

                    }
                });
            }
            jfbc.login.login_started = false;
        },

        // Deprecated, used on_login()
        login_button_click:function ()
        {
            jfbc.login.on_login();
        },

        logout_button_click:function ()
        {
            if (jfbc.login.logout_facebook)
            {
                FB.getLoginStatus(function (response)
                {
                    if (response.status === 'connected')
                    {
                        FB.logout(function (response)
                        {
                            jfbc.login.redirect_to_logout();
                        });
                    }
                    else
                    {
                        jfbc.login.redirect_to_logout();
                    }
                });
            }
            else
            {
                jfbc.login.redirect_to_logout();
            }
        },

        redirect_to_logout:function ()
        {
            self.location = jfbc.base + 'index.php?option=com_jfbconnect&task=logout&return=' + jfbc.return_url;
        }
    },

    register:{
        checkUsernameAvailable:function ()
        {
            var testName = $('username').value;
            if (testName != '')
                var myXHR = new XHR({
                    method:'get',
                    onSuccess:jfbc.register.showUsernameSuccess
                }).send('index.php', 'option=com_jfbconnect&view=loginregister&task=checkUsernameAvailable&username=' + testName);
        },

        checkPassword:function ()
        {
            var testPassword = $('password').value;
            var passwordSuccessElement = $('jfbcPasswordSuccess');
            var val = "";
            if (testPassword.length < 6)
                val = '<img src="' + jfbc.base + 'images/cancel_f2.png" width="20" height="20">' + jfbcPasswordInvalid;
            passwordSuccessElement.innerHTML = val;
        },

        checkPassword2:function ()
        {
            var testPassword = $('password').value;
            var testPassword2 = $('password2').value;
            var passwordSuccessElement = $('jfbcPassword2Success');
            var val = "";
            if (testPassword != testPassword2)
                val = '<img src="' + jfbc.base + 'images/cancel_f2.png" width="20" height="20">' + jfbcPassword2NoMatch;
            passwordSuccessElement.innerHTML = val;
        },

        showUsernameSuccess:function (req)
        {
            var usernameSuccessElement = $('jfbcUsernameSuccess');
            if (req == 1)
            {
                usernameSuccessElement.innerHTML = '<img src="' + jfbc.base + 'images/apply_f2.png" width="20" height="20">' + jfbcUsernameIsAvailable;
            }
            else
            {
                usernameSuccessElement.innerHTML = '<img src="' + jfbc.base + 'images/cancel_f2.png" width="20" height="20">' + jfbcUsernameIsInUse;
            }

        },

        checkEmailAvailable:function ()
        {
            var testEmail = $('email').value;
            if (testEmail != '' && jfbc.register.isEmail(testEmail))
                var myXHR = new XHR({
                    method:'get',
                    onSuccess:jfbc.register.showEmailSuccess
                }).send('index.php', 'option=com_jfbconnect&view=loginregister&task=checkEmailAvailable&email=' + testEmail);
        },

        showEmailSuccess:function (req)
        {
            emailSuccessElement = document.getElementById('jfbcEmailSuccess');
            if (req == 1)
            {
                emailSuccessElement.innerHTML = '<img src="' + jfbc.base + 'images/apply_f2.png" width="20" height="20">' + jfbcEmailIsAvailable;
            }
            else
            {
                emailSuccessElement.innerHTML = '<img src="' + jfbc.base + 'images/cancel_f2.png" width="20" height="20">' + jfbcEmailIsInUse;
            }
        },

        isEmail:function (text)
        {
            var pattern = "^[\\w-_\.]*[\\w-_\.]\@[\\w]\.+[\\w]+[\\w]$";
            var regex = new RegExp(pattern);
            return regex.test(text);
        }
    },

    social:{
        comment:{
            create:function (response)
            {
                var url = 'option=com_jfbconnect&controller=social&task=commentCreate&href=' + encodeURIComponent(escape(response.href)) + '&commentID=' + response.commentID;
                jfbc.ajax(url, null);
            }
        },
        like:{
            create:function (response)
            {
                var url = 'option=com_jfbconnect&controller=social&task=likeCreate&href=' + encodeURIComponent(escape(response));
                jfbc.ajax(url, null);
            }
        },
        /**
         * Tracks Facebook likes, unlikes and sends by suscribing to the Facebook
         * JSAPI event model. Note: This will not track facebook buttons using the
         * iFrame method.
         */
        googleAnalytics:{
            trackFacebook:function ()
            {
                var opt_pageUrl = window.location;
                try
                {
                    if (FB && FB.Event && FB.Event.subscribe)
                    {
                        FB.Event.subscribe('edge.create', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'like',
                                targetUrl, opt_pageUrl]);
                        });
                        FB.Event.subscribe('edge.remove', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'unlike',
                                targetUrl, opt_pageUrl]);
                        });
                        FB.Event.subscribe('message.send', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'send',
                                targetUrl, opt_pageUrl]);
                        });
                        FB.Event.subscribe('comment.create', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'comment',
                                targetUrl, opt_pageUrl]);
                        });
                        FB.Event.subscribe('comment.remove', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'uncomment',
                                targetUrl, opt_pageUrl]);
                        });
                    }
                }
                catch (e)
                {
                }
            }
        },

        // Not published yet. Need to figure out the best way to incorporate this into pages
        feedPost:function (title, caption, description, url, picture)
        {
//            javascript:jfbc.social.feedPost('SourceCoast JE', 'My caption', 'Great extensions!', 'http://www.sourcecoast.com/', 'https://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.png');
            var obj = {
                method:'feed',
                link:url,
                picture:picture,
                name: title, // page title?
                caption:caption,
                description:description
            };

            function callback(response)
            {
            }

            FB.ui(obj, callback);
        }
    },

    canvas:{
        checkFrame:function ()
        {
            if (top == window)
            { // crude check for any frame
                if (window.location.search == "")
                    top.location.href = window.location.href + '?jfbcCanvasBreakout=1';
                else
                    top.location.href = window.location.href + '&jfbcCanvasBreakout=1';
            }
        }
    },

    request:{
        currentId:null,
        popup:function (jfbcReqId)
        {
            jfbc.request.currentId = jfbcReqId;
            data = jfbcRequests[jfbcReqId];
            FB.ui({method:'apprequests', display:'popup',
                message:data.message,
                title:data.title,
                data:jfbcReqId
            }, jfbc.request.fbCallback);

        },

        fbCallback:function (response)
        {
            if (response != null)
            {
                var rId = response.request;
                var to = response.to;

                var toQuery = "";
                for (var i = 0; i < to.length; i++)
                    toQuery += "&to[]=" + to[i];

                var query = 'option=com_jfbconnect&controller=request&task=requestSent&requestId=' + rId + toQuery + '&jfbcId=' + jfbc.request.currentId;
                jfbc.ajax(query, jfbc.request.redirectToThanks);
            }
        },

        redirectToThanks:function ()
        {
            data = jfbcRequests[jfbc.request.currentId];
            if (data.thanksUrl != "" && (window.location.href != data.thanksUrl))
                window.location.href = data.thanksUrl;
        }
    },

    ajax:function (url, callback)
    {
        var mooVer = MooTools.version.split(".");
        if (mooVer[1] == '2' || mooVer[1] == '3' || mooVer[1] == '4') // Joomla 1.5 w/mtupgrade or J1.6+
        {
            var req = new Request({
                method:'get',
                url:jfbc.base + 'index.php?' + url,
                onSuccess:callback
            }).send();
        }
        else
        {
            var myXHR = new XHR({
                method:'get',
                onSuccess:callback
            }).send(jfbc.base + 'index.php', url);
        }
    }
};
