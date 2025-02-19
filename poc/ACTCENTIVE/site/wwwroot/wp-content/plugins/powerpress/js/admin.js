function powerpress_verifyMedia(el) {
    let feed_slug = el.id.replace("verify-button-", "");
    powerpress_get_media_info(feed_slug);
}


/**
 * Sanitize and encode all HTML in a user-submitted string to prevent XSS
 * https://portswigger.net/web-security/cross-site-scripting/preventing
 * @param  {String} str  The user-submitted string
 * @return {String} str  The sanitized string
 */
var sanitizeHTML = function (str) {
    if (str === undefined) {
        return str;
    }
    // allowed characters: letters, numbers, colon, backslash, question mark, dash, equals, ampersand
    return str.replace(/[^\w\d:\/\?\-=.& ]/gi, function (c) {
        return '&#' + c.charCodeAt(0) + ';';
    });
};

var interval = false;
var verify_interval = false;

jQuery(window).on("load", function(){
    let url = sanitizeHTML(jQuery("#verify-account-url").val());
    if (url) {
        tb_show('Verify Blubrry Account', url + '&KeepThis=true&TB_iframe=true&width=600&height=400&modal=true', false);
        jQuery('#adminmenuwrap, #adminmenuwrap > *, #wpadminbar, #wpadminbar > *').css('z-index', '200000');
        let height = jQuery('#wpwrap').height();
        jQuery('#TB_overlay').css('height', height.toString() + 'px');
        jQuery('body.modal-open').css('overflow-y', 'scroll');
        jQuery('#TB_window, #TB_window iframe').css('height', '400px');
        jQuery('#TB_window, #TB_window iframe').css('width', '800px');
        jQuery('#TB_window').css('margin-left', '-315px');
        jQuery('#TB_window').css('margin-top', '-220px');
        jQuery('#TB_window').css('top', '50%');
    }
    return false;
});

function powerpress_toggle_lock_section(evt) {
    if (evt.currentTarget.checked) {
        jQuery('#pp-feed-lock-section').css('display', 'block');
    } else {
        jQuery('#pp-feed-lock-section').css('display', 'none');
    }
}

function powerpress_toggle_guid_section(evt) {
    if (evt.currentTarget.checked) {
        jQuery('#pp-guid-override-section').css('display', 'block');
    } else {
        jQuery('#pp-guid-override-section').css('display', 'none');
    }
}

function powerpress_openTab(evt, cityName) {
    // Declare all variables
    var tabcontent, tablinks;

    let feed_slug = event.currentTarget.id.substring(1);
    evt.preventDefault();

    let desired_tab = jQuery("#" + event.currentTarget.id);
    let id = "#" + cityName;
    let desired_tab_contents = jQuery(id);

    // Get all elements with class="pp-tabcontent" and hide them
    tabcontent = jQuery('.pp-tabcontent');
    tabcontent.each(function(index, element) {
        //jQuery(this).css("display", "none");
        jQuery(this).attr("class", "pp-tabcontent has-sidenav");
    });

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = jQuery(".tablinks");
    tablinks.each(function(index, element) {
        jQuery(this).attr("class", "tablinks");
    });

    // Show the current tab, and add an "active" class to the button that opened the tab
    desired_tab_contents.attr("class", "pp-tabcontent has-sidenav active");
    desired_tab.attr("class", "tablinks active");

    //Set/unset the interval for updating artwork previews
    if (cityName == 'artwork-' + feed_slug) {
        let el = jQuery("#powerpress_itunes_image_" + feed_slug);
        let el_a = jQuery("#powerpress_image_" + feed_slug);
        jQuery.merge(el, el_a);
        if (el.length > 0) {
            interval = setInterval(function () {
                powerpress_insertArtIntoPreview(el[0]);
            }, 1000);
        }
    }
    if (cityName != 'artwork-' + feed_slug && interval) {
        clearInterval(interval);
        interval = false;
    }

    //In Settings tabs, need to set the sidenav
    if (cityName.includes("settings")) {
        let settingsTab = cityName.replace("settings-", "");
        switch(settingsTab) {
            case "welcome":
                document.getElementById("welcome-default-open").click();
                break;
            case "feeds":
                document.getElementById("feeds-default-open").click();
                break;
            case "website":
                document.getElementById("website-default-open").click();
                break;
            case "destinations":
                document.getElementById("destinations-default-open").click();
                break;
            case "analytics":

                break;
            case "advanced":
                document.getElementById("advanced-default-open").click();
                break;
            case "other":
                document.getElementById("other-default-open").click();
                break;
            case "make-money":
                document.getElementById("make-money-default-open").click();
                break;
            case "live-item":
                document.getElementById("live-item-default-open").click();
                break;
            case "experimental":
                document.getElementById("experimental-default-open").click();
                break;
            default:
                break;
        }
    }
}

function powerpress_displaySideNav(el) {
    let tab = el.id.replace("-toggle-sidenav", "");
    let sidenav = jQuery('#settings-' + tab + ' .pp-sidenav');
    if (sidenav.length > 0) {
        sidenav.each(function(index, element) {
            jQuery(this).attr('class', 'pp-sidenav-hidden');
            el.innerText = ">";
        });
        let visible_tab_contents = jQuery('.pp-sidenav-tab.to-be-active');
        visible_tab_contents.each(function(index, element) {
            jQuery(this).removeClass('to-be-active');
            jQuery(this).addClass('active')
        });
    } else {
        sidenav = jQuery('#settings-' + tab + ' .pp-sidenav-hidden');
        sidenav.each(function(index, element) {
            jQuery(this).attr('class', 'pp-sidenav');
            el.innerText = "<";
        });
        let visible_tab_contents = jQuery('.pp-sidenav-tab.active');
        visible_tab_contents.each(function(index, element) {
            jQuery(this).removeClass('active');
            jQuery(this).addClass('to-be-active')
        });
    }
}

function sideNav(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks, tabs;
    let target;
    evt.preventDefault();

    if (event.currentTarget.id == "pp-welcome-artwork-link") {
        target = document.getElementById("feeds-artwork-tab");
    } else if(event.currentTarget.id == "pp-welcome-applesubmit-link"){
        target = document.getElementById("destinations-apple-tab");
    } else if(event.currentTarget.id == "advanced-tab-seo-link"){
        target = document.getElementById("feeds-seo-tab");
    }
    else {
        target = event.currentTarget;
    }
    let desired_tab = jQuery("#" + target.id);
    let id = "#" + cityName;
    let desired_tab_contents = jQuery(id);

    let icon = target.firstElementChild;

    // Check if we have the sidnav open
    let toggle_id = target.id.split("-");
    let to_be_active = jQuery('#settings-' + toggle_id[0] + ' .pp-sidenav');
    //Hide the sidenav if necessary
    let width = jQuery(window).width();
    if (width < 650 && to_be_active.length > 0) {
        jQuery('#' + toggle_id[0] + '-toggle-sidenav').click();
    }

    // Get all elements with class="pp-tabcontent" and hide them
    tabcontent = jQuery(".pp-sidenav-tab");
    tabcontent.each(function(index, element) {
        jQuery(this).attr("class", "pp-sidenav-tab");
    });

    // Get all elements with class="tablinks" and remove the class "active"
    tabs = jQuery(".pp-sidenav-tablinks");
    tabs.each(function(index, element) {
        jQuery(this).attr("class", jQuery(this).attr("class").replace('active', ''));
    });

    tablinks = document.getElementsByClassName("pp-sidenav-tablinks");
    if (!cityName.includes("destinations")) {
        for (i = 0; i < tablinks.length; i++) {
            //Set any icons that are blue back to gray
            let img_file = tablinks[i].firstElementChild.getAttribute("src");
            if (img_file && img_file.includes("blue")) {
                let new_img_file = img_file.replace("blue", "gray");
                tablinks[i].firstElementChild.setAttribute("src", new_img_file);
            }
        }

        if (cityName != "feeds-apple") {
            //Set the selected icon to blue
            let img_file = icon.getAttribute("src");
            let new_img_file = img_file.replace("gray", "blue");
            icon.setAttribute("src", new_img_file);
        }
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    desired_tab_contents.attr("class", "pp-sidenav-tab active");
    desired_tab.attr("class", desired_tab.attr("class") + ' active');

}

//Controls the three-way explicit setting switch
function powerpress_changeExplicitSwitch(el) {
    let id = el.id;
    let feed_slug = id.replace("clean", "");
    feed_slug = feed_slug.replace("explicit", "");
    let clean = jQuery('#clean' + feed_slug);
    clean.removeAttr("class");
    clean.removeAttr("style");
    let explicit = jQuery('#explicit' + feed_slug);
    explicit.removeAttr("class");
    explicit.removeAttr("style");
    let select = jQuery('#pp-explicit-container' + feed_slug + ' > input');if (id.includes("clean")) {
        clean.attr("class", "explicit-selected");
        explicit.attr("class", "pp-explicit-option");
        explicit.attr("style", "border-left: 1px solid #b3b3b3");
        select.val(2);
    } else if (id.includes("explicit")) {
        clean.attr("class", "pp-explicit-option");
        clean.attr("style", "border-right: 1px solid #b3b3b3;");
        explicit.attr("class", "explicit-selected");
        select.val(1);
    }
}

function powerpress_toggleMetamarksSettings(el) {
    var feed_slug;
    let row_num_array = el.id.split("-");
    let row_num = row_num_array[3];
    let feed_slug_array = row_num_array.splice(4, 1);
    if (feed_slug_array.length > 1) {
        feed_slug = feed_slug_array.join("-");
    } else {
        feed_slug = feed_slug_array[0];
    }
    let button_id = "#" + el.id;
    let button = jQuery(button_id);
    let div_id = "#pp-hide-metamark-" + feed_slug + "-" + row_num;
    let type_select_id = "#pp-metamark-type-" + feed_slug + "-" + row_num;
    let type_preview_id = "#pp-metamark-preview-type--" + feed_slug + "-" + row_num;
    let pos_input_id = "#pp-metamark-pos-" + feed_slug + "-" + row_num;
    let pos_preview_id = "#pp-metamark-preview-pos-" + feed_slug + "-" + row_num;
    jQuery(div_id).toggleClass('pp-hidden-settings');
    if (button.text().includes('Edit')) {
        button.html("Save");
    } else {
        button.html("Edit");
        let type = jQuery(type_select_id).find(":selected").text();
        let pos = sanitizeHTML(jQuery(pos_input_id).val());
        jQuery(type_preview_id).html(type);
        jQuery(pos_preview_id).html(pos);
    }
}

function powerpress_verifyButtonColor(feed_slug) {
    let verify_button = jQuery("#save-media-" + feed_slug);
    let other_v_button = jQuery("#continue-to-episode-settings-" + feed_slug);
    if (jQuery("#powerpress_url_" + feed_slug).val().length > 0) {
        verify_button.attr("style", "background-color: #0c74d5; color: white;");
        other_v_button.attr("style", "background-color: #0c74d5; color: white;");
    } else {
        verify_button.attr("style", "background-color: #fafafa; color: #263238; border: 1px solid black;");
        other_v_button.attr("style", "background-color: #fafafa; color: #263238; border: 1px solid black;");
    }
}

function powerpress_showHideMediaDetails(el) {
    let feed_slug = el.id.replace("show-details-link-", "");
    let show_det = jQuery("#show-details-link-" + feed_slug);
    let div = jQuery("#hidden-media-details-" + feed_slug);
    show_det.toggleClass('pp-hidden-settings');
    show_det.toggleClass('media-details');
    div.toggleClass('pp-hidden-settings');
}

function powerpress_showHideAppleAdvanced(el) {
    let feed_slug;
    let show_det;
    let new_id;
    if (el.id.includes("show")) {
        feed_slug = el.id.replace("show-apple-link-", "");
        show_det = jQuery("#show-apple-link-" + feed_slug);
        show_det.attr("aria-pressed", "true");
        new_id = "hide-apple-link-" + feed_slug;
        show_det.html(hide_settings);
    } else {
        feed_slug = el.id.replace("hide-apple-link-", "");
        show_det = jQuery("#hide-apple-link-" + feed_slug);
        show_det.attr("aria-pressed", "false");
        new_id = "show-apple-link-" + feed_slug;
        show_det.html(show_settings);
    }
    el.id = new_id;
    let div = jQuery("#apple-advanced-settings-" + feed_slug);
    div.toggleClass('pp-hidden-settings');
}

function powerpress_changeMediaFile(evt, el) {
    evt.preventDefault();
    let feed_slug = el.id.replace("pp-edit-media-button-", "");
    let input = jQuery("#pp-url-input-container-" + feed_slug);
    input.removeAttr("style");
    input.attr("style", "display: inline-block");
    let show_input = jQuery("#powerpress_url_show_" + feed_slug);
    show_input.removeAttr("style");
    show_input.attr("style", "display: none");
    let edit_media = jQuery("#edit-media-file-" + feed_slug);
    edit_media.removeAttr("style");
    edit_media.attr("style", "display: none");
    let buttons = jQuery("#pp-change-media-file-" + feed_slug);
    buttons.removeAttr("style");
    buttons.attr("style", "display: inline-block");
    let blubrry_info = jQuery("#ep-box-blubrry-service-" + feed_slug);
    blubrry_info.removeAttr("style");
    blubrry_info.attr("style", "display: block");
    let container = jQuery("#pp-media-blubrry-container-" + feed_slug);
    container.attr("style", "background-color: #f1f4f9; padding: 2ch;");
    if(!verify_interval) {
        verify_interval = setInterval(function() { powerpress_verifyButtonColor(feed_slug); })
    }
}

//save button for edit media link
function powerpress_cancelMediaEdit(el) {
    let feed_slug = el.id.replace("cancel-media-edit-", "");
    let display_filename = jQuery("#ep-box-filename-" + feed_slug);
    let link = sanitizeHTML(display_filename.val());
    let input = jQuery("#pp-url-input-container-" + feed_slug);
    let url_field = jQuery("#powerpress_url_" + feed_slug + " > input");
    let show_input = jQuery("#powerpress_url_show_" + feed_slug);
    let edit_media = jQuery("#edit-media-file-" + feed_slug);
    let select_file = jQuery("#select-media-file-" + feed_slug);
    let buttons = jQuery("#pp-change-media-file-" + feed_slug);
    let warning = jQuery("#file-change-warning-" + feed_slug);
    let blubrry_info = jQuery("#ep-box-blubrry-service-" + feed_slug);
    let container = jQuery("#pp-media-blubrry-container-" + feed_slug);
    if(verify_interval) {
        clearInterval(verify_interval);
        verify_interval = false;
    }
    url_field.val(link);
    warning.css('display', 'none');
    input.removeAttr("style");
    input.attr("style", "display: none");
    show_input.css("display", "inline-block");
    edit_media.removeAttr("style");
    edit_media.attr("style", "display: inline-block");
    select_file.removeAttr("style");
    select_file.attr("style", "display: none");
    buttons.removeAttr("style");
    buttons.attr("style", "display: none");
    blubrry_info.removeAttr("style");
    blubrry_info.attr("style", "display: none");
    container.removeAttr("style");
}

//save button for edit media link
function powerpress_saveMediaFile(el) {
    let feed_slug = el.id.replace("save-media-", "");
    powerpress_get_media_info(feed_slug);
    let link = sanitizeHTML(jQuery("#powerpress_url_display_" + feed_slug).val());
    let display_filename = jQuery("#ep-box-filename-" + feed_slug);
    let input = jQuery("#pp-url-input-container-" + feed_slug);
    let show_input = jQuery("#powerpress_url_show_" + feed_slug);
    let edit_media = jQuery("#edit-media-file-" + feed_slug);
    let buttons = jQuery("#pp-change-media-file-" + feed_slug);
    let warning = jQuery("#file-change-warning-" + feed_slug);
    let blubrry_info = jQuery("#ep-box-blubrry-service-" + feed_slug);
    let container = jQuery("#pp-media-blubrry-container-" + feed_slug);
    if (link !== '') {
        if(verify_interval) {
            clearInterval(verify_interval);
            verify_interval = false;
        }
        show_input.attr("title", link);
        display_filename.html(link);
        warning.css('display', 'none');
        input.removeAttr("style");
        input.attr("style", "display: none");
        show_input.css("display", "inline-block");
        edit_media.removeAttr("style");
        edit_media.attr("style", "display: inline-block");
        buttons.removeAttr("style");
        buttons.attr("style", "display: none");
        blubrry_info.removeAttr("style");
        blubrry_info.attr("style", "display: none");
        container.removeAttr("style");
    } else {
        warning.css('display', 'block');
        warning.addClass("error");
    }
}

//Display all tabs along with an empty field for media
function powerpress_skipToEpisodeSettings(feed_slug) {
    let tab_container = jQuery("#tab-container-" + feed_slug);
    let warning = jQuery("#file-select-warning-" + feed_slug);
    let details = jQuery("#media-file-details-" + feed_slug);
    let blu_container = jQuery("#pp-media-blubrry-container-" + feed_slug);
    let connect_info = jQuery("#ep-box-blubrry-connect-" + feed_slug);
    let connect_info_small = jQuery("#ep-box-min-blubrry-connect-" + feed_slug);
    tab_container.removeAttr("style");
    tab_container.attr("style", "display: block");
    warning.removeAttr("style");
    warning.attr("style", "display: none");
    details.removeAttr("style");
    details.attr("style", "display: inline-block");
    blu_container.removeAttr("style");
    blu_container.attr("style", "background-color: #f1f4f9; padding: 2ch;");
    if(verify_interval) {
        clearInterval(verify_interval);
        verify_interval = false;
    }
    if (connect_info.length) {
        connect_info.attr("style", "display: none");
        connect_info_small.removeAttr("style");
    }
}

// event listener to ensure that the hidden media URL input matches the other
function powerpress_updateMediaInput(el) {
    let hidden_input_id = el.id.replace("display_", "");
    jQuery('#' + hidden_input_id).val(el.value);
}

//Continue button for adding media to a post
function powerpress_continueToEpisodeSettings(el) {
    let feed_slug = el.id.replace("continue-to-episode-settings-", "");
    powerpress_get_media_info(feed_slug);
    let link = sanitizeHTML(jQuery("#powerpress_url_display_" + feed_slug).val());
    let file_input = jQuery("#pp-url-input-container-" + feed_slug);
    let file_show = jQuery("#powerpress_url_show_" + feed_slug);
    let display_filename = jQuery("#ep-box-filename-" + feed_slug);
    let tab_container = jQuery("#tab-container-" + feed_slug);
    let warning = jQuery("#file-select-warning-" + feed_slug);
    let edit_file = jQuery("#edit-media-file-" + feed_slug);
    let select_file = jQuery("#select-media-file-" + feed_slug);
    let details = jQuery("#media-file-details-" + feed_slug);
    let blubrry_info = jQuery("#ep-box-blubrry-service-" + feed_slug);
    let blu_container = jQuery("#pp-media-blubrry-container-" + feed_slug);
    let connect_info = jQuery("#ep-box-blubrry-connect-" + feed_slug);
    let connect_info_small = jQuery("#ep-box-min-blubrry-connect-" + feed_slug);
    if (link.length > 0) {
        if(verify_interval) {
            clearInterval(verify_interval);
            verify_interval = false;
        }
        file_show.attr("title", link);
        display_filename.html(link);
        tab_container.removeAttr("style");
        tab_container.attr("style", "display: block");
        select_file.removeAttr("style");
        select_file.attr("style", "display: none");
        edit_file.removeAttr("style");
        edit_file.attr("style", "display: inline-block");
        file_input.removeAttr("style");
        file_input.attr("style", "display: none");
        file_show.css("display", "inline-block");
        warning.removeAttr("style");
        warning.attr("style", "display: none");
        blubrry_info.removeAttr("style");
        blubrry_info.attr("style", "display: none");
        details.removeAttr("style");
        details.attr("style", "display: inline-block");
        blu_container.removeAttr("style");
        if (connect_info.length) {
            connect_info.attr("style", "display: none");
            connect_info_small.removeAttr("style");
        }
    } else {
        warning.css('display', 'block');
        warning.addClass("error");
    }
}

//keeps art previews up to date
function powerpress_insertArtIntoPreview(el) {
    let feed_slug = el.id.replace("powerpress_itunes_image_", "");
    feed_slug = feed_slug.replace("powerpress_image_", "");
    let art_input = "#powerpress_itunes_image_" + feed_slug;
    let poster_input = "#powerpress_image_" + feed_slug;
    let episode_artwork = jQuery(art_input);
    let img_tag = jQuery("#pp-image-preview-" + feed_slug);
    let caption_tag = jQuery("#pp-image-preview-caption-" + feed_slug);
    let poster_image = jQuery(poster_input);
    let poster_img_tag = jQuery("#poster-pp-image-preview-" + feed_slug);
    let poster_caption_tag = jQuery("#poster-pp-image-preview-caption-" + feed_slug);
    let new_poster_image = sanitizeHTML(poster_image.val());
    if (poster_img_tag.attr("src") != new_poster_image && new_poster_image.length > 0) {
        poster_img_tag.attr("src", new_poster_image);
        let filename = "";
        if (new_poster_image.includes("/")) {
            let parts = new_poster_image.split("/");
            filename = parts.pop();
        } else {
            let parts = new_poster_image.split("\\");
            filename = parts.pop();
        }
        poster_caption_tag[0].innerHTML = filename;
    }
    let new_ep_image = sanitizeHTML(episode_artwork.val());
    if (img_tag.attr("src") != new_ep_image && new_ep_image.length > 0) {
        img_tag.attr("src", new_ep_image);
        let filename = "";
        if (new_ep_image.includes("/")) {
            let parts = new_ep_image.split("/");
            filename = parts.pop();
        } else {
            let parts = new_ep_image.split("\\");
            filename = parts.pop();
        }
        caption_tag[0].innerHTML = filename;
    }
}

//Display geo and osm settings if text is entered into the location setting
function powerpress_locationInput(event){
    let el = event.currentTarget;
    let location_details = jQuery("#pp-location-details");
    if (el.value.length == 0) {
        location_details.removeAttr("style");
        location_details.attr("style", "display: none");
    } else if (el.value.length > 0) {
        location_details.removeAttr("style");
        location_details.attr("style", "display: block");
    }
}

//Display inputs if users check a box to enable an episode-level podcast index setting
function powerpress_epboxPCIToggle(el){
    let id_array = el.id.split("_");
    id_array[4] = id_array[3];
    id_array[3] = "container";
    let target_id = id_array.join("_");
    let target_element = jQuery("#" + target_id);
    if (el.checked) {
        target_element.removeAttr("style");
        target_element.attr("style", "display: block");
        jQuery('#' + id_array[4] + '-chapter-builder-container').css("display", "block");
    } else {
        target_element.removeAttr("style");
        target_element.attr("style", "display: none");
        jQuery('#' + id_array[4] + '-chapter-builder-container').css("display", "none");
    }
}

function updateImFilename(event) {
    const [file] = event.files;

    let fileContainer = jQuery(event).parent().parent();
    let imContainer = fileContainer.parent().find('#preview-im-container');

    imContainer.find('#preview-im').attr('src', URL.createObjectURL(file));

    fileContainer.attr('style', 'display: none !important;');
    imContainer.attr('style', 'display: block;');
}

function removeIm(event) {
    let imContainer = jQuery(event).parent();
    let im = imContainer.find('#preview-im');
    let fileContainer = imContainer.parent().find('#upload-im-container');
    let fileInp = fileContainer.find('#file-inp');
    let removeIm = imContainer.parent().find('#remove-existing');

    fileInp.val('');
    removeIm.val('1');

    fileContainer.attr('style', 'display: block;');
    imContainer.attr('style', 'display: none !important;');
    im.attr('src', '');
}

function powerpress_epboxPCIToggleManual(el){
    let id_array = el.id.split("_");
    if (el.checked) {
        jQuery('#' + id_array[3] + '-chapter-builder').css("display", "block");
    } else {
        jQuery('#' + id_array[3] + '-chapter-builder').css("display", "none");
    }
}

function setTranscriptCheckboxes(id, feed_slug){
    let noTranscript = jQuery('#powerpress_transcript_none_' + feed_slug);
    let generateTranscript = jQuery('#powerpress_transcript_generate_' + feed_slug);
    let uploadTranscript = jQuery('#powerpress_pci_transcript_' + feed_slug);
    let uploadTranscriptInput = jQuery('#powerpress_pci_transcript_container_' + feed_slug);
    let generateTranscriptLanguage = jQuery('#pp-generate-language-' + feed_slug);
    let uploadTranscriptLanguage = jQuery('#pp-upload-language-' + feed_slug);
    let generateTranscriptLanguageContainer = jQuery('#powerpress_generate_transcript_container_' + feed_slug);

    switch(id){
        case 'powerpress_transcript_none_' + feed_slug:
            generateTranscript.attr("checked", false);
            uploadTranscript.attr("checked", false);
            if(typeof jQuery.prop === 'function') {
                generateTranscript.prop("checked", false);
                uploadTranscript.prop("checked", false);
            }
            uploadTranscriptInput.removeAttr("style");
            uploadTranscriptInput.attr("style", "display: none");
            generateTranscriptLanguageContainer.removeAttr("style");
            generateTranscriptLanguageContainer.attr("style", "display: none");
            uploadTranscriptLanguage.attr('disabled', true);
            generateTranscriptLanguage.attr('disabled', true);
            break;

        case 'powerpress_transcript_generate_' + feed_slug:
            noTranscript.attr("checked", false);
            uploadTranscript.attr("checked", false);
            if(typeof jQuery.prop === 'function') {
                noTranscript.prop("checked", false);
                uploadTranscript.prop("checked", false);
            }
            uploadTranscriptInput.removeAttr("style");
            uploadTranscriptInput.attr("style", "display: none");
            generateTranscriptLanguageContainer.removeAttr("style");
            generateTranscriptLanguageContainer.attr("style", "display: block");
            uploadTranscriptLanguage.attr('disabled', true);
            generateTranscriptLanguage.removeAttr('disabled');
            break;

        case 'powerpress_pci_transcript_' + feed_slug:

            noTranscript.attr("checked", false);
            generateTranscript.attr("checked", false);
            if(typeof jQuery.prop === 'function') {
                noTranscript.prop("checked", false);
                generateTranscript.prop("checked", false);
            }
            uploadTranscriptInput.removeAttr("style");
            uploadTranscriptInput.attr("style", "display: block");
            generateTranscriptLanguageContainer.removeAttr("style");
            generateTranscriptLanguageContainer.attr("style", "display: none");
            uploadTranscriptLanguage.removeAttr('disabled');
            generateTranscriptLanguage.attr('disabled', true);
            break;
    }
}

function showHideTranscriptBox(id, feed_slug){
    let optionsBox = jQuery('#transcript-box-options-' + feed_slug);
    if(optionsBox.is(':hidden')){
        optionsBox.prop('style', 'margin-top: 1.5em;display: block;');
        if(typeof jQuery.prop === 'function') {
            optionsBox.prop('style', 'margin-top: 1.5em;display: block;');
        }
    } else {
        optionsBox.attr('style', 'display: none;');
        if(typeof jQuery.prop === 'function') {
            optionsBox.prop('style', 'display: none;');
        }
    }
}

function unlinkAccount(idForm)
{
    let unlinkInput = jQuery('<input name="unlinkAccount" hidden>');
    jQuery(function($){ $('#' + idForm).append(unlinkInput) });
    jQuery(function($){ $('#' + idForm).attr('action', '#/') });
}

function unlinkNetwork()
{ //Add an input of unlink to form and submit
    jQuery(function($){ $('#linkNetwork').attr('value', 'unlink') });
}

function directStatus (status, idForm, changeOrCreate = false, tab = 'shows')
{
    if (changeOrCreate){
        let input = jQuery('<input name="changeOrCreate" value=true hidden>');
        jQuery(function($){ $('#' + idForm).append(input) });
    }
    jQuery(function($){ $('#' + idForm).attr('action', '?page=network-plugin&status='+status + '&tab=' + tab) });
    jQuery(function($){ $('#'+idForm).unbind('submit') });
    jQuery(function($){ $('#'+idForm).submit() });
}

function toggle(id, subItem = '')
{

    if (document.getElementById(subItem + id).style.display === "none")
        jQuery(function($){ $('#toggle' + subItem + id).html('more_vert') });
    else {
        ('#toggle' + subItem + id).html('more_horiz');
        if (subItem === '') {
            jQuery(function($){ $('#shortCode' + id).hide() });
            jQuery(function($){ $('#toggle' + 'shortCode' + id).html('more_vert') });
        }
    }
    jQuery(function($){ $('#'+ subItem + id).slideToggle() });
}

function showPPNTab (application)
{
    let x = document.getElementsByClassName("tabContent");
    let i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(application).style.display = "block";
    jQuery(function($){ $(".tabActive").addClass("tabInactive") });
    jQuery(function($){ $(".tabActive").removeClass("tabActive") });
    jQuery(function($){ $("#" + application + "Tab").removeClass("tabInactive") });
    jQuery(function($){ $("#" + application + "Tab").addClass("tabActive") });
}

function createPage(id, target, idForm, pageTitle = false)
{
    let content = '';
    if (target === 'Program') {
        if (pageTitle === false) {
            pageTitle = 'program id = ' + id;
        }
        content = '[ppn-program id = ' + id + '] ';
    } else if (target === 'List') {
        if (pageTitle === false) {
            pageTitle = 'list id = ' + id;
        }
        content = '[ppn-list id = ' + id + '] ';
    }
    let addElement = jQuery('<input name="target" value="' + target + '" hidden>' +
        '<input name="targetId" value=' + id + ' hidden>' +
        '<input name="content" value="' + content + '" hidden>'+
        '<input name="pageTitle" value="' + pageTitle + '" hidden>');
    jQuery(function($){ $('#' + idForm).append (addElement) });
    directStatus('Manage ' + target, idForm);
}

function createApplicationPage(target, idForm, pageTitle = false)
{
    let content = '';
    if (target === 'Application') {
        if (pageTitle === false) {
            pageTitle = 'Application Page';
        }
        content = '[ppn-application terms-url=]';
    }

    let addElement = jQuery('<input name="target" value="' + target + '" hidden>' +
        '<input name="content" value="' + content + '" hidden>' +
        '<input name="pageTitle" value="' + pageTitle + '" hidden>');
    jQuery(function($) { $('#' + idForm).append (addElement) });
    directStatus('Manage ' + target, idForm);
}

function confirmUnlink(idForm)
{
    let unlink = jQuery('<input name="pageAction" value="unlink" hidden>');
    jQuery(function($){ $("#"+idForm).append(unlink) });
}

function refreshAndCallDirectAPI(currentPage, idForm, tab = 'shows', changeOrCreate = false)
{
    let refresh = jQuery('<input name = "pageAction" value= "clearSiteCache" hidden>') ;
    jQuery(function($){ $("#"+idForm).append(refresh) });
    let clear = jQuery('<input name = "clearSiteCache" value= "true" hidden>') ;
    jQuery(function($){ $("#"+idForm).append(clear) });
    jQuery("#"+idForm).append(refresh);
    directStatus(currentPage, idForm, changeOrCreate, tab);
}

function manageProgram(programId, linkPage)
{
    jQuery(function($){ $('#programId').attr('value', programId) });
    jQuery(function($){ $('#linkPageProgram').attr('value', linkPage) });
}

function manageList(listId, linkPage = false)
{
    jQuery(function($){ $('#listId').attr('value', listId) });
    jQuery(function($){ $('#linkPageList').attr('value', linkPage) });
}

function updateListOfShows(in_list, title) {
    if (in_list) {
        jQuery('#shows-in-group').html(jQuery('#shows-in-group').html() + '<li><span>' + title + '</span></li>');
    } else {
        jQuery('#shows-in-group').html(jQuery('#shows-in-group').html().replace('<li><span>' + title + '</span></li>', ''));
    }
}

function showSelectPage()
{
    jQuery(function($){ $(".selectPageBox").show() });
    jQuery(function($){ $("#choiceBox").hide() });
}
function showSelectChoice()
{
    jQuery(function($){ $(".selectPageBox").hide() });
    jQuery(function($){ $("#choiceBox").show() });
}
function showConfirmUnlink()
{
    jQuery(function($){ $("#choiceBox").hide() });
    jQuery(function($){ $(".confirmUnlink").show() });
}

function approveProgram (applicantId, approve = true, undo = false, deleteApplicant = false){
    let addInfo = null;
    if (approve != false)
        addInfo = jQuery ('<input name ="appAction" value="approve" hidden>');
    else if (undo != false)
        addInfo = jQuery ('<input name ="appAction" value="undo" hidden>');
    else if (deleteApplicant != false)
        addInfo = jQuery ('<input name ="appAction" value="delete" hidden>');
    else
        addInfo = jQuery ('<input name ="appAction" value="disapprove" hidden>');
    let applicantInfo = jQuery ('<input name ="applicantId" value='+ applicantId+' hidden>');
    jQuery(function($){ $('#applicationCreateForm').append(addInfo).append(applicantInfo) });
    directStatus('Select Choice', 'applicationCreateForm', true, 'requests');
}

function editPageForProgram(programId, linkPage) {
    jQuery('#select-page-target-id').val(programId);
    jQuery('#page-select-ppn').val(linkPage);
    jQuery('#ppn-program-shortcode').val('[ppn-program id="' +programId + '"');
}

function confirmRemovalOfProgram(programId)
{
    let url = window.location.href;
    jQuery(function($){ $('#removeForm' + programId + ' .requestAction').attr('value', 'delete') });
    jQuery(($) => {
        $.ajax({
            type: "POST",
            url: url,
            data: {'program_id' : programId},
        }).done(() => {
            refreshAndCallDirectAPI('Select Choice', 'removeForm' + programId, 'shows', true);
        });
    });
}

