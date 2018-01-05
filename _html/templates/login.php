<?php 
    include "inc/header.inc.php";
    include "modules/header.php";
    include "modules/location-intro.php";
?>

<div class="sponsors-contact">
    <div class="l-container">
        <div class="small-title-block">
            <form class="user-login-form" data-drupal-selector="user-login-form" action="/user/login" method="post" id="user-login-form" accept-charset="UTF-8">
                <div class="js-form-item form-item js-form-type-textfield form-type-textfield js-form-item-name form-item-name">
                    <label for="edit-name" class="js-form-required form-required">Username</label>
                    <input autocorrect="none" autocapitalize="none" spellcheck="false" autofocus="autofocus" data-drupal-selector="edit-name" aria-describedby="edit-name--description" type="text" id="edit-name" name="name" value="" size="60" maxlength="60" class="form-text required" required="required" aria-required="true">
                    <div id="edit-name--description" class="description">
                        Enter your DrupalCamp Transylvania username.
                    </div>
                </div>
                <div class="js-form-item form-item js-form-type-password form-type-password js-form-item-pass form-item-pass">
                    <label for="edit-pass" class="js-form-required form-required">Password</label>
                    <input data-drupal-selector="edit-pass" aria-describedby="edit-pass--description" type="password" id="edit-pass" name="pass" size="60" maxlength="128" class="form-text required" required="required" aria-required="true">
                    <div id="edit-pass--description" class="description">
                        Enter the password that accompanies your username.
                    </div>
                </div>
                <input autocomplete="off" data-drupal-selector="form-yvvjbmhwueadncnmqcmy5scv2wf002jcjsj9vrrrp3u" type="hidden" name="form_build_id" value="form-YvVJBMHWuEadncNMqcmy5Scv2WF002jCjSJ9VRRrp3U">
                <input data-drupal-selector="edit-user-login-form" type="hidden" name="form_id" value="user_login_form">
                <div data-drupal-selector="edit-actions" class="form-actions js-form-wrapper form-wrapper" id="edit-actions"><input data-drupal-selector="edit-submit" type="submit" id="edit-submit" name="op" value="Log in" class="button js-form-submit form-submit">
                </div>
            </form>
            <div class="forgot-password">
                <a href="/user/password">Forgot password?</a>
            </div>
        </div>
    </div>
</div>

<?php 
    include "modules/footer.php";
    include "inc/footer.inc.php";
?>
