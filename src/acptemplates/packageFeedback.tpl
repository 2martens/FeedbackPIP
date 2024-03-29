{include file='setupWindowHeader'}

{if $errorField}
	<p class="error">{lang}wcf.acp.package.feedback.error{/lang}</p>
{/if}

<form method="post" action="index.php?page=Package">
    <fieldset>
        <legend>{lang}wcf.acp.package.feedback{/lang}</legend>
        <div class="inner">
            <p>{lang}wcf.acp.package.feedback.description{/lang}</p>
            
            <div{if $errorField == 'userEmail'} class="errorField"{/if}>
                <label for="userEmail">{lang}wcf.acp.package.feedback.email{/lang}{if $userEmailOptional} (optional){/if}</label>
                <input type="text" class="inputText" id="userEmail" name="userEmail" value="{@$userEmail}" />
            	{if $errorField == 'userEmail'}
					<p>
						<img src="{@RELATIVE_WCF_DIR}icon/errorS.png" alt="" />
						{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
						{if $errorType == 'notValid'}{lang}wcf.user.error.email.notValid{/lang}{/if}
					</p>
				{/if}
            </div>
            
            <div{if $errorField == 'feedback'} class="errorField"{/if}>
            	<label for="feedback">{lang}wcf.acp.package.feedback.text{/lang}</label>
            	<textarea class="inputText" id="feedback" name="feedback" rows="20" onclick="empty()">{@$feedback}</textarea>
            	{if $errorField == 'feedback'}
					<p>
						<img src="{@RELATIVE_WCF_DIR}icon/errorS.png" alt="" />
						{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
					</p>
				{/if}
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
	parent.showWindow(true);
    parent.setCurrentStep('{lang}wcf.acp.package.step.title{/lang}{lang}wcf.acp.package.step.{if $action == 'rollback'}uninstall{else}{@$action}{/if}.{@$step}{/lang}');
    //]]>
</script>

{include file='setupWindowFooter'}