<?php 
/** 
* Sponsors template.
*/

include "inc/header.inc.php";
include "modules/header.php";?>

    <main class="clearfix" role="main">
        <div class="region region-content">
            <div id="block-mainpagecontent" class="block block-system block-system-main-block proposal-form-container">
                <form class="contact-message-session-proposal-form-form contact-message-form contact-form" data-drupal-selector="contact-message-session-proposal-form-form" enctype="multipart/form-data" action="/contact/session_proposal_form" method="post" id="contact-message-session-proposal-form-form" accept-charset="UTF-8">
                    <div class="field--type-string field--name-field-name field--widget-string-textfield js-form-wrapper form-wrapper" data-drupal-selector="edit-field-name-wrapper" id="edit-field-name-wrapper">
                        <div class="js-form-item form-item js-form-type-textfield form-type-textfield js-form-item-field-name-0-value form-item-field-name-0-value">
                            <label for="edit-field-name-0-value" class="js-form-required form-required">Session/workshop title</label>
                            <input class="js-text-full text-full form-text required" data-drupal-selector="edit-field-name-0-value" type="text" id="edit-field-name-0-value" name="field_name[0][value]" value="" size="60" maxlength="255" placeholder="" required="required" aria-required="true">
                        </div>
                    </div>
                    <input autocomplete="off" data-drupal-selector="form-7k1hz6i9v2hfppazllqvquaedkgwhwcuj8vyenm9c-u" type="hidden" name="form_build_id" value="form-7K1hZ6i9v2hFppAzLlqvquAedKGwhwCuj8VyENM9c-U">
                    <input data-drupal-selector="edit-contact-message-session-proposal-form-form-form-token" type="hidden" name="form_token" value="qeBEIqfh7IyuoCUBEkzCxzUnC_H_E-EsGxC9hEjck10">
                    <input data-drupal-selector="edit-contact-message-session-proposal-form-form" type="hidden" name="form_id" value="contact_message_session_proposal_form_form">
                    <div class="field--type-entity-reference field--name-field-user field--widget-entity-reference-autocomplete js-form-wrapper form-wrapper" data-drupal-selector="edit-field-user-wrapper" id="edit-field-user-wrapper"><div id="field-user-add-more-wrapper">  
                        <div class="js-form-item form-item">
                            <div class="tabledrag-toggle-weight-wrapper">
                                <button type="button" class="link tabledrag-toggle-weight" title="Re-order rows by numerical weight instead of dragging.">Show row weights</button>
                            </div>
                            <div class="tableresponsive-toggle-columns">
                                <button type="button" class="link tableresponsive-toggle" title="Show table cells that were hidden to make the table fit within a small screen." style="display: none;">Hide lower priority columns</button>
                            </div>
                            <table id="field-user-values" class="field-multiple-table responsive-enabled" aria-describedby="edit-field-user--description" data-striping="1">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="field-label">
                                            <h4 class="label js-form-required form-required">Speakers</h4>
                                        </th>
                                        <th class="tabledrag-hide" style="display: none;">Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="draggable odd">
                                        <td class="field-multiple-drag">
                                            <a href="#" class="tabledrag-handle" title="Drag to re-order">
                                                <div class="handle">&nbsp;</div>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="js-form-item form-item js-form-type-entity-autocomplete form-type-entity-autocomplete js-form-item-field-user-0-target-id form-item-field-user-0-target-id form-no-label">
                                                <label for="edit-field-user-0-target-id" class="visually-hidden js-form-required form-required">Speakers (value 1)</label>
                                                <input data-drupal-selector="edit-field-user-0-target-id" class="form-autocomplete form-text required ui-autocomplete-input" data-autocomplete-path="/entity_reference_autocomplete/user/default%3Auser/0VfVXfvg4DzzWAMM4iAa8NAp2jp_Xy2IM5LWW3jhMrw" type="text" id="edit-field-user-0-target-id" name="field_user[0][target_id]" value="" size="60" maxlength="1024" placeholder="" required="required" aria-required="true" autocomplete="off">
                                            </div>
                                        </td>
                                        <td class="delta-order tabledrag-hide" style="display: none;">
                                            <div class="js-form-item form-item js-form-type-select form-type-select js-form-item-field-user-0--weight form-item-field-user-0--weight form-no-label">
                                                <label for="edit-field-user-0-weight" class="visually-hidden">Weight for row 1</label>
                                                <select data-drupal-selector="edit-field-user-0-weight" class="field_user-delta-order form-select" id="edit-field-user-0-weight" name="field_user[0][_weight]"><option value="0" selected="selected">0</option></select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div id="edit-field-user--description" class="description">
                                Type the username of the speaker, and to add more speakers click "Add another".
                            </div>
                            <div class="clearfix field-add-more-submit-wrapper">
                                <input class="field-add-more-submit button js-form-submit form-submit" data-drupal-selector="edit-field-user-add-more" formnovalidate="formnovalidate" type="submit" id="edit-field-user-add-more" name="field_user_add_more" value="Add another item">
                            </div>
                        </div>
                    </div>
                    <div class="field--type-string-long field--name-field-description field--widget-string-textarea js-form-wrapper form-wrapper" data-drupal-selector="edit-field-description-wrapper" id="edit-field-description-wrapper">
                        <div class="js-form-item form-item js-form-type-textarea form-type-textarea js-form-item-field-description-0-value form-item-field-description-0-value">
                            <label for="edit-field-description-0-value" class="js-form-required form-required">Description</label>
                            <div class="form-textarea-wrapper">
                                <textarea class="js-text-full text-full form-textarea required resize-vertical" data-drupal-selector="edit-field-description-0-value" aria-describedby="edit-field-description-0-value--description" id="edit-field-description-0-value" name="field_description[0][value]" rows="5" cols="60" placeholder="" required="required" aria-required="true"></textarea>
                            </div>
                            <div id="edit-field-description-0-value--description" class="description">
                                Please give us a detailed overview of your session and why attendees will be excited to hear about it.
                                Ensure that you let us know:
                                - What level of knowledge should attendees have before walking into your session
                                - What will your session accomplish and what will attendees walk away having learned
                            </div>
                        </div>
                    </div>
                    <div class="field--type-language field--name-langcode field--widget-language-select js-form-wrapper form-wrapper" data-drupal-selector="edit-langcode-wrapper" id="edit-langcode-wrapper">      
                    </div>
                    <div class="field--type-list-string field--name-field-select-list field--widget-options-select js-form-wrapper form-wrapper session-selection" data-drupal-selector="edit-field-select-list-wrapper" id="edit-field-select-list-wrapper">
                        <div class="js-form-item form-item js-form-type-select form-type-select js-form-item-field-select-list form-item-field-select-list">
                            <label for="edit-field-select-list" class="js-form-required form-required">Session type</label>
                            <select data-drupal-selector="edit-field-select-list" id="edit-field-select-list" name="field_select_list" class="form-select required" required="required" aria-required="true"><option value="Session" selected="selected">Session</option><option value="Workshop">Workshop</option></select>
                        </div>
                    </div>
                    <div class="field--type-list-string field--name-field-duration field--widget-options-buttons js-form-wrapper form-wrapper session-duration" id="session-duration" data-drupal-selector="edit-field-duration-wrapper">
                        <fieldset data-drupal-selector="edit-field-duration" id="edit-field-duration--wrapper" class="fieldgroup form-composite required js-form-item form-item js-form-wrapper form-wrapper" required="required" aria-required="true">
                            <legend>
                                <span class="fieldset-legend js-form-required form-required">Duration</span>
                            </legend>
                            <div class="fieldset-wrapper">
                                <div id="edit-field-duration" class="form-radios">
                                    <div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-field-duration form-item-field-duration">
                                        <input data-drupal-selector="edit-field-duration-25" type="radio" id="edit-field-duration-25" name="field_duration" value="25" checked="checked" class="form-radio">
                                        <label for="edit-field-duration-25" class="option">25</label>
                                    </div>
                                    <div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-field-duration form-item-field-duration">
                                        <input data-drupal-selector="edit-field-duration-50" type="radio" id="edit-field-duration-50" name="field_duration" value="50" class="form-radio">
                                        <label for="edit-field-duration-50" class="option">50</label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="field--type-file field--name-field-file-upload field--widget-file-generic js-form-wrapper form-wrapper" data-drupal-selector="edit-field-file-upload-wrapper" id="edit-field-file-upload-wrapper">
                        <div id="ajax-wrapper">
                            <div class="js-form-item form-item js-form-type-managed-file form-type-managed-file js-form-item-field-file-upload-0 form-item-field-file-upload-0">
                                <label for="edit-field-file-upload-0-upload">Slides</label>
                                <div id="edit-field-file-upload-0-upload" class="js-form-managed-file form-managed-file">
                                    <div id="ajax-wrapper"><input data-drupal-selector="edit-field-file-upload-0-upload" type="file" id="edit-field-file-upload-0-upload" name="files[field_file_upload_0]" size="22" class="js-form-file form-file">
                                        <input class="js-hide button js-form-submit form-submit" data-drupal-selector="edit-field-file-upload-0-upload-button" formnovalidate="formnovalidate" type="submit" id="edit-field-file-upload-0-upload-button" name="field_file_upload_0_upload_button" value="Upload">
                                        <input data-drupal-selector="edit-field-file-upload-0-fids" type="hidden" name="field_file_upload[0][fids]">
                                        <input data-drupal-selector="edit-field-file-upload-0-display" type="hidden" name="field_file_upload[0][display]" value="1">
                                    </div>
                                </div>
                                <div id="edit-field-file-upload-0-upload--description" class="description">
                                    Upload your slides here as a PDF, or link to them from the description. (This is typically done after your session.)<br>One file only.<br>2 MB limit.<br>Allowed types: txt ppt pdf odt odp.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field--type-list-string field--name-field-select field--widget-options-select js-form-wrapper form-wrapper" data-drupal-selector="edit-field-select-wrapper" id="edit-field-select-wrapper">
                        <div class="js-form-item form-item js-form-type-select form-type-select js-form-item-field-select form-item-field-select session-selection">
                            <label for="edit-field-select" class="js-form-required form-required">Drupal version</label>
                            <select data-drupal-selector="edit-field-select" id="edit-field-select" name="field_select" class="form-select required" required="required" aria-required="true">
                                <option value="_none">- Select a value -</option>
                                <option value="7.x">7.x</option>
                                <option value="8.x">8.x</option>
                                <option value="8.5.x">8.5.x</option>
                            </select>
                        </div>
                        <div class="field--type-entity-reference field--name-field-taxonomy-reference field--widget-entity-reference-autocomplete js-form-wrapper form-wrapper session-duration" data-drupal-selector="edit-field-taxonomy-reference-wrapper" id="edit-field-taxonomy-reference-wrapper">
                            <div class="js-form-item form-item js-form-type-entity-autocomplete form-type-entity-autocomplete js-form-item-field-taxonomy-reference-0-target-id form-item-field-taxonomy-reference-0-target-id">
                                <label for="edit-field-taxonomy-reference-0-target-id" class="js-form-required form-required">Session track</label>
                                <input data-drupal-selector="edit-field-taxonomy-reference-0-target-id" class="form-autocomplete form-text required ui-autocomplete-input" data-autocomplete-path="/entity_reference_autocomplete/taxonomy_term/default%3Ataxonomy_term/8QD6ipgRjVKLWobtKLBiwsRV6XWMCpyIAbmAaG0J2ww" type="text" id="edit-field-taxonomy-reference-0-target-id" name="field_taxonomy_reference[0][target_id]" value="" size="60" maxlength="1024" placeholder="" required="required" aria-required="true" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="field--type-string-long field--name-field-long-description field--widget-string-textarea js-form-wrapper form-wrapper" data-drupal-selector="edit-field-long-description-wrapper" id="edit-field-long-description-wrapper">
                        <div class="js-form-item form-item js-form-type-textarea form-type-textarea js-form-item-field-long-description-0-value form-item-field-long-description-0-value">
                            <label for="edit-field-long-description-0-value">Tell us about your experience as a speaker</label>
                            <div class="form-textarea-wrapper">
                                <textarea class="js-text-full text-full form-textarea resize-vertical" data-drupal-selector="edit-field-long-description-0-value" aria-describedby="edit-field-long-description-0-value--description" id="edit-field-long-description-0-value" name="field_long_description[0][value]" rows="5" cols="60" placeholder=""></textarea>
                            </div>
                            <div id="edit-field-long-description-0-value--description" class="description">
                                Please list or describe your speaking experience, including any presentation recording links. This information will not be displayed publicly and will only be used for reference by the session selection team.
                            </div>
                        </div>
                    </div>
                    <div class="field--type-list-string field--name-field-radio-button field--widget-options-buttons js-form-wrapper form-wrapper session-level" data-drupal-selector="edit-field-radio-button-wrapper" id="edit-field-radio-button-wrapper">
                        <fieldset data-drupal-selector="edit-field-radio-button" id="edit-field-radio-button--wrapper" class="fieldgroup form-composite required js-form-item form-item js-form-wrapper form-wrapper" required="required" aria-required="true">
                            <legend>
                                <span class="fieldset-legend js-form-required form-required">Audience</span>
                            </legend>
                            <div class="fieldset-wrapper">
                                <div id="edit-field-radio-button" class="form-radios">
                                    <div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-field-radio-button form-item-field-radio-button">
                                        <input data-drupal-selector="edit-field-radio-button-beginner" type="radio" id="edit-field-radio-button-beginner" name="field_radio_button" value="Beginner" class="form-radio">
                                        <label for="edit-field-radio-button-beginner" class="option">Beginner</label>
                                    </div>
                                    <div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-field-radio-button form-item-field-radio-button">
                                        <input data-drupal-selector="edit-field-radio-button-intermediate" type="radio" id="edit-field-radio-button-intermediate" name="field_radio_button" value="Intermediate" class="form-radio">
                                        <label for="edit-field-radio-button-intermediate" class="option">Intermediate</label>
                                    </div>
                                    <div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-field-radio-button form-item-field-radio-button">
                                        <input data-drupal-selector="edit-field-radio-button-advanced" type="radio" id="edit-field-radio-button-advanced" name="field_radio_button" value="Advanced" class="form-radio">
                                        <label for="edit-field-radio-button-advanced" class="option">Advanced</label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="field--type-boolean field--name-field-agree field--widget-boolean-checkbox js-form-wrapper form-wrapper" data-drupal-selector="edit-field-agree-wrapper" id="edit-field-agree-wrapper">
                        <div class="js-form-item form-item js-form-type-checkbox form-type-checkbox js-form-item-field-agree-value form-item-field-agree-value">
                            <input data-drupal-selector="edit-field-agree-value" aria-describedby="edit-field-agree-value--description" type="checkbox" id="edit-field-agree-value" name="field_agree[value]" value="1" class="form-checkbox">
                            <label for="edit-field-agree-value" class="option">Agree</label>
                            <div id="edit-field-agree-value--description" class="description">
                                By submitting this session I acknowledge that I have read and agree to the terms outlined in the Speaker Agreement document.
                            </div>
                        </div>
                    </div>
                    <div class="field--type-string field--name-field-node-id field--widget-string-textfield js-form-wrapper form-wrapper" data-drupal-selector="edit-field-node-id-wrapper" id="edit-field-node-id-wrapper">
                        <div class="js-form-item form-item js-form-type-textfield form-type-textfield js-form-item-field-node-id-0-value form-item-field-node-id-0-value">
                            <label for="edit-field-node-id-0-value">Node id</label>
                            <input class="js-text-full text-full form-text" data-drupal-selector="edit-field-node-id-0-value" type="text" id="edit-field-node-id-0-value" name="field_node_id[0][value]" value="" size="60" maxlength="255" placeholder="">
                        </div>
                    </div>
                    <div data-drupal-selector="edit-actions" class="form-actions js-form-wrapper form-wrapper" id="edit-actions">
                        <input data-drupal-selector="edit-submit" type="submit" id="edit-submit" name="op" value="Send message" class="button button--primary js-form-submit form-submit">
                    </div>
                </form>
            </div>
        </div>
    </main>
<?php 
include "modules/footer.php";
include "inc/footer.inc.php";
?>
