{include file='setupWindowHeader'}

<form method="post" action="index.php?page=Package">
    <fieldset>
        <legend>{lang}wcf.acp.package.feedback{/lang}</legend>
        <div class="inner">
            <p>{lang}wcf.acp.package.feedback.description{/lang}</p>
            
            <div class="inner">
               <textarea class="inputText" id="feedback" name="feedback" rows="20" onclick="empty()">{lang}wcf.acp.package.feedback.insert{/lang}</textarea>
            </div>
            
            <input type="hidden" name="queueID" value="{@$queueID}" />
            <input type="hidden" name="action" value="{@$action}" />
            {@SID_INPUT_TAG}
            <input type="hidden" name="step" value="{@$step}" />
            <input type="hidden" name="once" value="1" />
            <input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
            <input type="hidden" name="send" value="send" />
        </div>
    </fieldset>
    
    <div class="nextButton">
        <input type="submit" value="{lang}wcf.global.button.next{/lang}" onclick="parent.stopAnimating();" />
    </div>
</form>

<script type="text/javascript">
    //<![CDATA[
    window.onload = function() {
    changeHeight();
};
	var once = false;
	function empty() {
		if (once) return;
		var text = document.getElementById("feedback");
		text.value = "";
		once = true;
	}
    parent.showWindow(true);
    parent.setCurrentStep('{lang}wcf.acp.package.step.title{/lang}{lang}wcf.acp.package.step.{if $action == 'rollback'}uninstall{else}{@$action}{/if}.{@$step}{/lang}');
    //]]>
</script>

{include file='setupWindowFooter'}