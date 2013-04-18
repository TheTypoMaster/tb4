/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var jfbcAdmin = {
    config: {
        // Show/Hide the options based on the Full User or Facebook Only features
        // type = 'fullJoomla' and 'facebookOnly'
        setUserCreation: function(type)
        {
            var typeHide = type == 'fullJoomla' ? 'facebookOnly' : 'fullJoomla';
            jfbcAdmin.config.showOptions(type, true);
            jfbcAdmin.config.showOptions(typeHide, false);
        },

        showOptions: function(className, showValue)
        {
            $$('.'+className).setStyle('display', showValue == true ? '' : 'none');
       }
        
    },
    request: {
        send: function(start)
        {
            var statusDiv = $('sendStatus');
            if (start)
                statusDiv.innerHTML = "<h2>Sending in progress...</h2>This may take a few minutes. Please do not navigate away from this page until complete!";
            jfbcAdmin.ajax('option=com_jfbconnect&controller=request&task=send', jfbcAdmin.request.updateStatus);
        },

        updateStatus: function(req)
        {
            var status;
            if (jfbcAdmin.utilities.getMooVer() == "1.6")
                status = JSON.decode(req);
            else
                status = Json.evaluate(req);
            var statusDiv = $('sendStatus');
            statusDiv.innerHTML = "<h2>Sending in progress...</h2>" +
                "This may take a few minutes. Please do not navigate away from this page until complete!<br/><br/>" +
                "<strong>Total Sent</strong>: " + status.sendCount + "<br/><i>(" + status.sendSuccess + " Successful / " +
                status.sendFail + " Fail)</i>";

            if (status.inProgress)
                jfbcAdmin.request.send(false);
            else
                statusDiv.innerHTML = "<h2>Send Complete!</h2>" +
                                "<strong>Total Sent</strong>: " + status.sendCount + "<br/><i>(" + status.sendSuccess + " Successful / " +
                                status.sendFail + " Fail)</i>";
        }
    },

    ajax:function (url, callback) {
        if (jfbcAdmin.utilities.getMooVer() == "1.6")
        {
            var req = new Request({
                method:'get',
                url: 'index.php?' + url,
                onSuccess:callback
            }).send();
        }
        else {
            var myXHR = new XHR({
                method:'get',
                onSuccess:callback
            }).send('index.php', url);
        }
    },

    utilities: {
        getMooVer: function()
        {
            var mooVer = MooTools.version.split(".");
            if (mooVer[1] == '2' || mooVer[1] == '3' || mooVer[1] == '4') // Joomla 1.5 w/mtupgrade or J1.6+
                return "1.6"
            else
                return "1.5";
        }
    },

    autotune: {
        enablePlugin: function(name, status)
        {
            form = document.getElementById('adminForm');
            form.pluginName.value = name;
            form.pluginStatus.value = status;
            form.task.value = 'publishPlugin';
            form.submit();
        }
    }
}