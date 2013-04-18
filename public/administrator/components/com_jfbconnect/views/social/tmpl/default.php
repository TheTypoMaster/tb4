<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
$model = $this->model;

$pane = JPane::getInstance('tabs');
?>
<script type="text/javascript">
    function toggleHide(rowId, styleType)
    {
        document.getElementById(rowId).style.display = styleType;
    }
</script>
<style type="text/css">
    div.config_setting {
        width: 225px;
    }
    div.config_option {
        width: 250px;
    }
    div.config_setting_option{
        width:475px;
    }
</style>

<?php

echo '<form method="post" id="adminForm" name="adminForm">';

echo $pane->startPane('content-pane');
echo $pane->startPanel('Content Plugin - Comments', 'social_content_comment');
?>
    <div>
        <div class="config_row">
            <div class="config_setting header">Article View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Number of Comments:</div>
            <div class="config_option">
                <input type="text" name="social_article_comment_max_num" value="<?php echo $model->getSetting('social_article_comment_max_num') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The number of comments to display in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_article_comment_width" value="<?php echo $model->getSetting('social_article_comment_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the frame, in pixels, in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_article_comment_color_scheme" class="radio">
                    <input type="radio" id="social_article_comment_color_schemeL" name="social_article_comment_color_scheme" value="light" <?php echo $model->getSetting('social_article_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_color_schemeL">Light</label>
                    <input type="radio" id="social_article_comment_color_schemeD" name="social_article_comment_color_scheme" value="dark" <?php echo $model->getSetting('social_article_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">Blog View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Number of Comments:</div>
            <div class="config_option">
                <input type="text" name="social_blog_comment_max_num" value="<?php echo $model->getSetting('social_blog_comment_max_num') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The number of comments to display in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_blog_comment_width" value="<?php echo $model->getSetting('social_blog_comment_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the frame, in pixels, in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_blog_comment_color_scheme" class="radio">
                    <input type="radio" id="social_blog_comment_color_schemeL" name="social_blog_comment_color_scheme" value="light" <?php echo $model->getSetting('social_blog_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_color_schemeL">Light</label>
                    <input type="radio" id="social_blog_comment_color_schemeD" name="social_blog_comment_color_scheme" value="dark" <?php echo $model->getSetting('social_blog_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in Article View:</div>
            <div class="config_option">
                <?php $socialCommentArticleView = $model->getSetting('social_comment_article_view');?>
                <select name="social_comment_article_view">
                    <option value="1" <?php echo ($socialCommentArticleView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialCommentArticleView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialCommentArticleView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialCommentArticleView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Article view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in Frontpage View:</div>
            <div class="config_option">
                <?php $socialCommentFrontPageView = $model->getSetting('social_comment_frontpage_view');?>
                <select name="social_comment_frontpage_view">
                    <option value="1" <?php echo ($socialCommentFrontPageView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialCommentFrontPageView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialCommentFrontPageView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialCommentFrontPageView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Frontpage view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in Category View:</div>
            <div class="config_option">
                <?php $socialCommentCategoryView = $model->getSetting('social_comment_category_view');?>
                <select name="social_comment_category_view">
                    <option value="1" <?php echo ($socialCommentCategoryView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialCommentCategoryView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialCommentCategoryView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialCommentCategoryView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Category view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <?php
        
        ?>
        <div class="config_row">
            <div class="config_setting">Show in Section View:</div>
            <div class="config_option">
                <?php $socialCommentSectionView = $model->getSetting('social_comment_section_view');?>
                <select name="social_comment_section_view">
                    <option value="1" <?php echo ($socialCommentSectionView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialCommentSectionView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialCommentSectionView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialCommentSectionView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Section view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting_option header">Section Setting</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting_option">
                <?php $sectType = $model->getSetting('social_comment_sect_include_type'); ?>
                <fieldset id="social_comment_sect_include_type" class="radio">
                    <input type="radio" id="social_comment_sect_include_type0" name="social_comment_sect_include_type" value="0" <?php echo ($sectType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_sect_ids', 'none')" />
                    <label for="social_comment_sect_include_type0">All</label>
                    <input type="radio" id="social_comment_sect_include_type1" name="social_comment_sect_include_type" value="1" <?php echo ($sectType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_sect_ids', '')" />
                    <label for="social_comment_sect_include_type1">Include</label>
                    <input type="radio" id="social_comment_sect_include_type2" name="social_comment_sect_include_type" value="2" <?php echo ($sectType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_sect_ids', '')" />
                    <label for="social_comment_sect_include_type2">Exclude</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="All::All sections will have comments<br/><br/>
                <strong>Include</strong><br/>Only specified sections will have comments<br/><br/>
                <strong>Exclude</strong><br/>All sections, except those specified, will have comments.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row" id="comment_sect_ids" style="display:<?php echo ($sectType == "0" ? 'none' : ''); ?>">
<?php
            $sectids = $model->getSetting('social_comment_sect_ids');
            $sections = unserialize($sectids);
            $query = "SELECT id, title FROM #__sections";
            $db =JFactory::getDBO();
            $db->setQuery($query);
            $sects = $db->loadAssocList();
            $attribs = 'multiple="multiple"';
            echo '<td>'.JHTML::_('select.genericlist',$sects, 'social_comment_sect_ids[]', $attribs, 'id', 'title', $sections, 'social_comment_sect_ids').'</td>';
?>
            <div style="clear:both"></div>
        </div>        
        <?php
         //SC15
        ?>
        <div class="config_row">
            <div class="config_setting_option header">Category Setting</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting_option">
                <?php $catType = $model->getSetting('social_comment_cat_include_type'); ?>
                <fieldset id="social_comment_cat_include_type" class="radio">
                    <input type="radio" id="social_comment_cat_include_type0" name="social_comment_cat_include_type" value="0" <?php echo ($catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', 'none')" />
                    <label for="social_comment_cat_include_type0">All</label>
                    <input type="radio" id="social_comment_cat_include_type1" name="social_comment_cat_include_type" value="1" <?php echo ($catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', '')" />
                    <label for="social_comment_cat_include_type1">Include</label>
                    <input type="radio" id="social_comment_cat_include_type2" name="social_comment_cat_include_type" value="2" <?php echo ($catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', '')" />
                    <label for="social_comment_cat_include_type2">Exclude</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="All::All categories will have comments<br/><br/>
                <strong>Include</strong><br/>Only specified categories will have comments<br/><br/>
                <strong>Exclude</strong><br/>All categories, except those specified, will have comments.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row" id="comment_cat_ids" style="display:<?php echo ($catType == "0" ? 'none' : ''); ?>">
<?php
            $catids = $model->getSetting('social_comment_cat_ids');
            $categories = unserialize($catids);

            
            $query = "SELECT id, title FROM #__categories";
            ; //SC15
             //SC16

            $db = JFactory::getDBO();
            $db->setQuery($query);
            $cats = $db->loadAssocList();
            $attribs = 'multiple="multiple"';
            echo '<td>' . JHTML::_('select.genericlist', $cats, 'social_comment_cat_ids[]', $attribs, 'id', 'title', $categories, 'social_comment_cat_ids') . '</td>';
?>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">Article Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Include:</div>
            <div class="config_option">
                <input type="text" name="social_comment_article_include_ids" value="<?php echo $model->getSetting('social_comment_article_include_ids'); ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="Comma-separated list of article IDs to specifically include">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Exclude:</div>
            <div class="config_option">
                <input type="text" name="social_comment_article_exclude_ids" value="<?php echo $model->getSetting('social_comment_article_exclude_ids'); ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="Comma-separated list of article IDs to specifically exclude">Info
            </div>
            <div style="clear:both"></div>
        </div>
    </div>

<?php
    echo $pane->endPanel();
    echo $pane->startPanel('Content Plugin - Like', 'social_content_like');
?>
    <div>
        <div class="config_row">
            <div class="config_setting header">Article View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Layout Style:</div>
            <div class="config_option">
                <select name="social_article_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_article_like_layout_style') == 'standard' ? 'selected' : ""; ?>>standard</option>
                    <option value="box_count" <?php echo $model->getSetting('social_article_like_layout_style') == 'box_count' ? 'selected' : ""; ?>>box_count</option>
                    <option value="button_count" <?php echo $model->getSetting('social_article_like_layout_style') == 'button_count' ? 'selected' : ""; ?>>button_count</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Determines the size and amount of social context next to the button in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Faces:</div>
            <div class="config_option">
                <fieldset id="social_article_like_show_faces" class="radio">
                    <input type="radio" id="social_article_like_show_faces1" name="social_article_like_show_faces" value="1" <?php echo $model->getSetting('social_article_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_faces1">Yes</label>
                    <input type="radio" id="social_article_like_show_faces0" name="social_article_like_show_faces" value="0" <?php echo $model->getSetting('social_article_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_faces0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show profile pictures below the button in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Send Button:</div>
            <div class="config_option">
                <fieldset id="social_article_like_show_send_button" class="radio">
                    <input type="radio" id="social_article_like_show_send_button1" name="social_article_like_show_send_button" value="1" <?php echo $model->getSetting('social_article_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_send_button1">Yes</label>
                    <input type="radio" id="social_article_like_show_send_button0" name="social_article_like_show_send_button" value="0" <?php echo $model->getSetting('social_article_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_send_button0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show send button next to like button in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show LinkedIn Button:</div>
            <div class="config_option">
                <fieldset id="social_article_like_show_linkedin" class="radio">
                    <input type="radio" id="social_article_like_show_linkedin1" name="social_article_like_show_linkedin" value="1" <?php echo $model->getSetting('social_article_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_linkedin1">Yes</label>
                    <input type="radio" id="social_article_like_show_linkedin0" name="social_article_like_show_linkedin" value="0" <?php echo $model->getSetting('social_article_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_linkedin0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show LinkedIn Share button when the Like button is displayed in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Twitter Button:</div>
            <div class="config_option">
                <fieldset id="social_article_like_show_twitter" class="radio">
                    <input type="radio" id="social_article_like_show_twitter1" name="social_article_like_show_twitter" value="1" <?php echo $model->getSetting('social_article_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_twitter1">Yes</label>
                    <input type="radio" id="social_article_like_show_twitter0" name="social_article_like_show_twitter" value="0" <?php echo $model->getSetting('social_article_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_twitter0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Twitter Share button when the Like button is displayed in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Google+ Button:</div>
            <div class="config_option">
                <fieldset id="social_article_like_show_googleplus" class="radio">
                    <input type="radio" id="social_article_like_show_googleplus1" name="social_article_like_show_googleplus" value="1" <?php echo $model->getSetting('social_article_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_googleplus1">Yes</label>
                    <input type="radio" id="social_article_like_show_googleplus0" name="social_article_like_show_googleplus" value="0" <?php echo $model->getSetting('social_article_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_googleplus0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Google +1 button when the Like button is displayed in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Verb to Display:</div>
            <div class="config_option">
                <fieldset id="social_article_like_verb_to_display" class="radio">
                    <input type="radio" id="social_article_like_verb_to_displayLike" name="social_article_like_verb_to_display" value="like" <?php echo $model->getSetting('social_article_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_verb_to_displayLike">Like</label>
                    <input type="radio" id="social_article_like_verb_to_displayRec" name="social_article_like_verb_to_display" value="recommend" <?php echo $model->getSetting('social_article_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_verb_to_displayRec">Recommend</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The verb to display in the button in the article view.  Currently only like and recommend are supported">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_article_like_color_scheme" class="radio">
                    <input type="radio" id="social_article_like_color_schemeL" name="social_article_like_color_scheme" value="light" <?php echo $model->getSetting('social_article_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_color_schemeL">Light</label>
                    <input type="radio" id="social_article_like_color_schemeD" name="social_article_like_color_scheme" value="dark" <?php echo $model->getSetting('social_article_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Font:</div>
            <div class="config_option">
                <select name="social_article_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_article_like_font') == 'arial' ? 'selected' : ""; ?>>Arial</option>
                    <option value="lucinda grande" <?php echo $model->getSetting('social_article_like_font') == 'lucinda grande' ? 'selected' : ""; ?>>Lucinda Grande</option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_article_like_font') == 'segoe ui' ? 'selected' : ""; ?>>Segoe UI</option>
                    <option value="tahoma" <?php echo $model->getSetting('social_article_like_font') == 'tahoma' ? 'selected' : ""; ?>>Tahoma</option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_article_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>>Trebuchet MS</option>
                    <option value="verdana" <?php echo $model->getSetting('social_article_like_font') == 'verdana' ? 'selected' : ""; ?>>Verdana</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="The font in the article view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_article_like_width" value="<?php echo $model->getSetting('social_article_like_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the Facebook Like Button frame, in pixels, in the article view.  This setting is only used with the 'standard' layout style.">Info
            </div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting header">Blog View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Layout Style:</div>
            <div class="config_option">
                <select name="social_blog_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_blog_like_layout_style') == 'standard' ? 'selected' : ""; ?>>standard</option>
                    <option value="box_count" <?php echo $model->getSetting('social_blog_like_layout_style') == 'box_count' ? 'selected' : ""; ?>>box_count</option>
                    <option value="button_count" <?php echo $model->getSetting('social_blog_like_layout_style') == 'button_count' ? 'selected' : ""; ?>>button_count</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Determines the size and amount of social context next to the button in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Faces:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_faces" class="radio">
                    <input type="radio" id="social_blog_like_show_faces1" name="social_blog_like_show_faces" value="1" <?php echo $model->getSetting('social_blog_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_faces1">Yes</label>
                    <input type="radio" id="social_blog_like_show_faces0" name="social_blog_like_show_faces" value="0" <?php echo $model->getSetting('social_blog_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_faces0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show profile pictures below the button  in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Send Button:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_send_button" class="radio">
                    <input type="radio" id="social_blog_like_show_send_button1" name="social_blog_like_show_send_button" value="1" <?php echo $model->getSetting('social_blog_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_send_button1">Yes</label>
                    <input type="radio" id="social_blog_like_show_send_button0" name="social_blog_like_show_send_button" value="0" <?php echo $model->getSetting('social_blog_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_send_button0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show send button next to like button  in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show LinkedIn Button:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_linkedin" class="radio">
                    <input type="radio" id="social_blog_like_show_linkedin1" name="social_blog_like_show_linkedin" value="1" <?php echo $model->getSetting('social_blog_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_linkedin1">Yes</label>
                    <input type="radio" id="social_blog_like_show_linkedin0" name="social_blog_like_show_linkedin" value="0" <?php echo $model->getSetting('social_blog_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_linkedin0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show LinkedIn Share button when the Like button is displayed in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Twitter Button:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_twitter" class="radio">
                    <input type="radio" id="social_blog_like_show_twitter1" name="social_blog_like_show_twitter" value="1" <?php echo $model->getSetting('social_blog_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_twitter1">Yes</label>
                    <input type="radio" id="social_blog_like_show_twitter0" name="social_blog_like_show_twitter" value="0" <?php echo $model->getSetting('social_blog_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_twitter0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Twitter Share button when the Like button is displayed in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Google+ Button:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_googleplus" class="radio">
                    <input type="radio" id="social_blog_like_show_googleplus1" name="social_blog_like_show_googleplus" value="1" <?php echo $model->getSetting('social_blog_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_googleplus1">Yes</label>
                    <input type="radio" id="social_blog_like_show_googleplus0" name="social_blog_like_show_googleplus" value="0" <?php echo $model->getSetting('social_blog_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_googleplus0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Google +1 button when the Like button is displayed in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Verb to Display:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_verb_to_display" class="radio">
                    <input type="radio" id="social_blog_like_verb_to_displayLike" name="social_blog_like_verb_to_display" value="like" <?php echo $model->getSetting('social_blog_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_verb_to_displayLike">Like</label>
                    <input type="radio" id="social_blog_like_verb_to_displayRec" name="social_blog_like_verb_to_display" value="recommend" <?php echo $model->getSetting('social_blog_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_verb_to_displayRec">Recommend</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The verb to display in the button in blog views.  Currently only like and recommend are supported">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_blog_like_color_scheme" class="radio">
                    <input type="radio" id="social_blog_like_color_schemeL" name="social_blog_like_color_scheme" value="light" <?php echo $model->getSetting('social_blog_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_color_schemeL">Light</label>
                    <input type="radio" id="social_blog_like_color_schemeD" name="social_blog_like_color_scheme" value="dark" <?php echo $model->getSetting('social_blog_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Font:</div>
            <div class="config_option">
                <select name="social_blog_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_blog_like_font') == 'arial' ? 'selected' : ""; ?>>Arial</option>
                    <option value="lucinda grande" <?php echo $model->getSetting('social_blog_like_font') == 'lucinda grande' ? 'selected' : ""; ?>>Lucinda Grande</option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_blog_like_font') == 'segoe ui' ? 'selected' : ""; ?>>Segoe UI</option>
                    <option value="tahoma" <?php echo $model->getSetting('social_blog_like_font') == 'tahoma' ? 'selected' : ""; ?>>Tahoma</option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_blog_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>>Trebuchet MS</option>
                    <option value="verdana" <?php echo $model->getSetting('social_blog_like_font') == 'verdana' ? 'selected' : ""; ?>>Verdana</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="The font in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_blog_like_width" value="<?php echo $model->getSetting('social_blog_like_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the Facebook Like Button frame, in pixels, in blog views.  This setting is only used with the 'standard' layout style.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in Article View:</div>
            <div class="config_option">
                <?php $socialLikeArticleView = $model->getSetting('social_like_article_view');?>
                <select name="social_like_article_view">
                    <option value="1" <?php echo ($socialLikeArticleView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialLikeArticleView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialLikeArticleView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialLikeArticleView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Article view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in Frontpage View:</div>
            <div class="config_option">
                <?php $socialLikeFrontPageView = $model->getSetting('social_like_frontpage_view');?>
                <select name="social_like_frontpage_view">
                    <option value="1" <?php echo ($socialLikeFrontPageView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialLikeFrontPageView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialLikeFrontPageView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialLikeFrontPageView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Frontpage view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in Category View:</div>
            <div class="config_option">
                <?php $socialLikeCategoryView = $model->getSetting('social_like_category_view');?>
                <select name="social_like_category_view">
                    <option value="1" <?php echo ($socialLikeCategoryView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialLikeCategoryView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialLikeCategoryView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialLikeCategoryView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in Category view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <?php
        
        ?>
        <div class="config_row">
            <div class="config_setting">Show in Section View:</div>
            <div class="config_option">
                <?php $socialLikeSectionView = $model->getSetting('social_like_section_view');?>
                <select name="social_like_section_view">
                    <option value="1" <?php echo ($socialLikeSectionView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialLikeSectionView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialLikeSectionView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialLikeSectionView == '0') ? 'selected' : ""; ?>>None</option>
                </select>                
            </div>
            <div class="config_description hasTip"
                title="Show in Section view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting_option header">Section Setting</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting_option">
                <?php $sectType = $model->getSetting('social_like_sect_include_type'); ?>
                <fieldset id="social_like_sect_include_type" class="radio">
                    <input type="radio" id="social_like_sect_include_type0" name="social_like_sect_include_type" value="0" <?php echo ($sectType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_sect_ids', 'none')" />
                    <label for="social_like_sect_include_type0">All</label>
                    <input type="radio" id="social_like_sect_include_type1" name="social_like_sect_include_type" value="1" <?php echo ($sectType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_sect_ids', '')" />
                    <label for="social_like_sect_include_type1">Include</label>
                    <input type="radio" id="social_like_sect_include_type2" name="social_like_sect_include_type" value="2" <?php echo ($sectType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_sect_ids', '')" />
                    <label for="social_like_sect_include_type2">Exclude</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="All::All sections will have the Like button<br/><br/>
                <strong>Include</strong><br/>Only specified sections will have the Like button<br/><br/>
                <strong>Exclude</strong><br/>All sections, except those specified, will have the Like button.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row" id="like_sect_ids" style="display:<?php echo ($sectType == "0" ? 'none' : ''); ?>">
<?php
            $sectids = $model->getSetting('social_like_sect_ids');
            $sections = unserialize($sectids);
            $query = "SELECT id, title FROM #__sections";
            $db =JFactory::getDBO();
            $db->setQuery($query);
            $sects = $db->loadAssocList();
            $attribs = 'multiple="multiple"';
            echo '<td>'.JHTML::_('select.genericlist',$sects, 'social_like_sect_ids[]', $attribs, 'id', 'title', $sections, 'social_like_sect_ids').'</td>';
?>
            <div style="clear:both"></div>
        </div>
        <?php
         //SC15
        ?>
        <div class="config_row">
            <div class="config_setting_option header">Category Setting</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting_option">
                <?php $catType = $model->getSetting('social_like_cat_include_type'); ?>
                <fieldset id="social_like_cat_include_type" class="radio">
                    <input type="radio" id="social_like_cat_include_type0" name="social_like_cat_include_type" value="0" <?php echo ($catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', 'none')" />
                    <label for="social_like_cat_include_type0">All</label>
                    <input type="radio" id="social_like_cat_include_type1" name="social_like_cat_include_type" value="1" <?php echo ($catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', '')" />
                    <label for="social_like_cat_include_type1">Include</label>
                    <input type="radio" id="social_like_cat_include_type2" name="social_like_cat_include_type" value="2" <?php echo ($catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', '')" />
                    <label for="social_like_cat_include_type2">Exclude</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="All::All categories will have the Like button<br/><br/>
                <strong>Include</strong><br/>Only specified categories will have the Like button<br/><br/>
                <strong>Exclude</strong><br/>All categories, except those specified, will have the Like button.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row" id="like_cat_ids" style="display:<?php echo ($catType == "0" ? 'none' : ''); ?>">
<?php
            $catids = $model->getSetting('social_like_cat_ids');
            $categories = unserialize($catids);

            
            $query = "SELECT id, title FROM #__categories";
             //SC15
             //SC16
            $db = JFactory::getDBO();
            $db->setQuery($query);
            $cats = $db->loadAssocList();
            $attribs = 'multiple="multiple"';
            echo '<td>' . JHTML::_('select.genericlist', $cats, 'social_like_cat_ids[]', $attribs, 'id', 'title', $categories, 'social_like_cat_ids') . '</td>';
?>
        <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">Article Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting">Include:</div>
            <div class="config_option">
                <input type="text" name="social_like_article_include_ids" value="<?php echo $model->getSetting('social_like_article_include_ids'); ?>" size="20">
                </div>
                <div class="config_description hasTip"
                    title="Comma-separated list of article IDs to specifically include">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Exclude:</div>
                <div class="config_option">
                    <input type="text" name="social_like_article_exclude_ids" value="<?php echo $model->getSetting('social_like_article_exclude_ids'); ?>" size="20">
                </div>
                <div class="config_description hasTip"
                    title="Comma-separated list of article IDs to specifically exclude">Info
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
<?php
    echo $pane->endPanel();

    $k2IsInstalled = JFolder::exists(JPATH_SITE.DS.'components'.DS.'com_k2');
    if($k2IsInstalled) {
    echo $pane->startPanel('Content Plugin - K2 Comments', 'social_content_k2_comment');
?>
    <div>
        <div class="config_row">
            <div class="config_setting header">Item View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Number of Comments:</div>
            <div class="config_option">
                <input type="text" name="social_k2_item_comment_max_num" value="<?php echo $model->getSetting('social_k2_item_comment_max_num') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The number of comments to display in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_k2_item_comment_width" value="<?php echo $model->getSetting('social_k2_item_comment_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the frame, in pixels, in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_comment_color_scheme" class="radio">
                    <input type="radio" id="social_k2_item_comment_color_schemeL" name="social_k2_item_comment_color_scheme" value="light" <?php echo $model->getSetting('social_k2_item_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_color_schemeL">Light</label>
                    <input type="radio" id="social_k2_item_comment_color_schemeD" name="social_k2_item_comment_color_scheme" value="dark" <?php echo $model->getSetting('social_k2_item_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">Blog View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Number of Comments:</div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_comment_max_num" value="<?php echo $model->getSetting('social_k2_blog_comment_max_num') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The number of comments to display in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_comment_width" value="<?php echo $model->getSetting('social_k2_blog_comment_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the frame, in pixels, in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_comment_color_scheme" class="radio">
                    <input type="radio" id="social_k2_blog_comment_color_schemeL" name="social_k2_blog_comment_color_scheme" value="light" <?php echo $model->getSetting('social_k2_blog_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_color_schemeL">Light</label>
                    <input type="radio" id="social_k2_blog_comment_color_schemeD" name="social_k2_blog_comment_color_scheme" value="dark" <?php echo $model->getSetting('social_k2_blog_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Item View:</div>
            <div class="config_option">
                <?php $socialK2CommentItemView = $model->getSetting('social_k2_comment_item_view');?>
                <select name="social_k2_comment_item_view">
                    <option value="1" <?php echo ($socialK2CommentItemView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2CommentItemView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2CommentItemView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2CommentItemView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Item view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Category View:</div>
            <div class="config_option">
                <?php $socialK2CommentCategoryView = $model->getSetting('social_k2_comment_category_view');?>
                <select name="social_k2_comment_category_view">
                    <option value="1" <?php echo ($socialK2CommentCategoryView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2CommentCategoryView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2CommentCategoryView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2CommentCategoryView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Category view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Tag View:</div>
            <div class="config_option">
                <?php $socialK2CommentTagView = $model->getSetting('social_k2_comment_tag_view');?>
                <select name="social_k2_comment_tag_view">
                    <option value="1" <?php echo ($socialK2CommentTagView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2CommentTagView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2CommentTagView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2CommentTagView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Tag view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Userpage View:</div>
            <div class="config_option">
                <?php $socialK2CommentUserpageView = $model->getSetting('social_k2_comment_userpage_view');?>
                <select name="social_k2_comment_userpage_view">
                    <option value="1" <?php echo ($socialK2CommentUserpageView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2CommentUserpageView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2CommentUserpageView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2CommentUserpageView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Userpage view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Latest View:</div>
            <div class="config_option">
                <?php $socialK2CommentLatestView = $model->getSetting('social_k2_comment_latest_view');?>
                <select name="social_k2_comment_latest_view">
                    <option value="1" <?php echo ($socialK2CommentLatestView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2CommentLatestView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2CommentLatestView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2CommentLatestView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Latest view when settings include the Comments">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <!-- Categories -->
        <div class="config_row">
            <div class="config_setting_option header">K2 Category Setting</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting_option">
                <?php $k2CatType = $model->getSetting('social_k2_comment_cat_include_type'); ?>
                <fieldset id="social_k2_comment_cat_include_type" class="radio">
                    <input type="radio" id="social_k2_comment_cat_include_type0" name="social_k2_comment_cat_include_type" value="0" <?php echo ($k2CatType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', 'none')" />
                    <label for="social_k2_comment_cat_include_type0">All</label>
                    <input type="radio" id="social_k2_comment_cat_include_type1" name="social_k2_comment_cat_include_type" value="1" <?php echo ($k2CatType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', '')" />
                    <label for="social_k2_comment_cat_include_type1">Include</label>
                    <input type="radio" id="social_k2_comment_cat_include_type2" name="social_k2_comment_cat_include_type" value="2" <?php echo ($k2CatType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', '')" />
                    <label for="social_k2_comment_cat_include_type2">Exclude</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="All::All categories will have comments<br/><br/>
                <strong>Include</strong><br/>Only specified categories will have comments<br/><br/>
                <strong>Exclude</strong><br/>All categories, except those specified, will have comments.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row" id="k2_comment_cat_ids" style="display:<?php echo ($k2CatType == "0" ? 'none' : ''); ?>">
<?php
            $k2catids = $model->getSetting('social_k2_comment_cat_ids');
            $k2categories = unserialize($k2catids);

            $query = "SELECT `id`, `name` FROM #__k2_categories";
            $db = JFactory::getDBO();
            $db->setQuery($query);
            $k2cats = $db->loadAssocList();
            $attribs = 'multiple="multiple"';
            echo '<td>' . JHTML::_('select.genericlist', $k2cats, 'social_k2_comment_cat_ids[]', $attribs, 'id', 'name', $k2categories, 'social_k2_comment_cat_ids') . '</td>';
?>
            <div style="clear:both"></div>
        </div>
        <!-- End Categories -->
        <div class="config_row">
            <div class="config_setting header">K2 Item Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Include:</div>
            <div class="config_option">
                <input type="text" name="social_k2_comment_item_include_ids" value="<?php echo $model->getSetting('social_k2_comment_item_include_ids'); ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="Comma-separated list of K2 Item IDs to specifically include">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Exclude:</div>
            <div class="config_option">
                <input type="text" name="social_k2_comment_item_exclude_ids" value="<?php echo $model->getSetting('social_k2_comment_item_exclude_ids'); ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="Comma-separated list of K2 Item IDs to specifically exclude">Info
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel('Content Plugin - K2 Like', 'social_content_k2_like');
?>
    <div>
        <div class="config_row">
            <div class="config_setting header">Item View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Layout Style:</div>
            <div class="config_option">
                <select name="social_k2_item_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'standard' ? 'selected' : ""; ?>>standard</option>
                    <option value="box_count" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'box_count' ? 'selected' : ""; ?>>box_count</option>
                    <option value="button_count" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'button_count' ? 'selected' : ""; ?>>button_count</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Determines the size and amount of social context next to the button in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Faces:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_faces" class="radio">
                    <input type="radio" id="social_k2_item_like_show_faces1" name="social_k2_item_like_show_faces" value="1" <?php echo $model->getSetting('social_k2_item_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_faces1">Yes</label>
                    <input type="radio" id="social_k2_item_like_show_faces0" name="social_k2_item_like_show_faces" value="0" <?php echo $model->getSetting('social_k2_item_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_faces0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show profile pictures below the button in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Send Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_send_button" class="radio">
                    <input type="radio" id="social_k2_item_like_show_send_button1" name="social_k2_item_like_show_send_button" value="1" <?php echo $model->getSetting('social_k2_item_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_send_button1">Yes</label>
                    <input type="radio" id="social_k2_item_like_show_send_button0" name="social_k2_item_like_show_send_button" value="0" <?php echo $model->getSetting('social_k2_item_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_send_button0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show send button next to like button in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show LinkedIn Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_linkedin" class="radio">
                    <input type="radio" id="social_k2_item_like_show_linkedin1" name="social_k2_item_like_show_linkedin" value="1" <?php echo $model->getSetting('social_k2_item_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_linkedin1">Yes</label>
                    <input type="radio" id="social_k2_item_like_show_linkedin0" name="social_k2_item_like_show_linkedin" value="0" <?php echo $model->getSetting('social_k2_item_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_linkedin0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show LinkedIn Share button when the Like button is displayed in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Twitter Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_twitter" class="radio">
                    <input type="radio" id="social_k2_item_like_show_twitter1" name="social_k2_item_like_show_twitter" value="1" <?php echo $model->getSetting('social_k2_item_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_twitter1">Yes</label>
                    <input type="radio" id="social_k2_item_like_show_twitter0" name="social_k2_item_like_show_twitter" value="0" <?php echo $model->getSetting('social_k2_item_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_twitter0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Twitter Share button when the Like button is displayed in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Google+ Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_googleplus" class="radio">
                    <input type="radio" id="social_k2_item_like_show_googleplus1" name="social_k2_item_like_show_googleplus" value="1" <?php echo $model->getSetting('social_k2_item_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_googleplus1">Yes</label>
                    <input type="radio" id="social_k2_item_like_show_googleplus0" name="social_k2_item_like_show_googleplus" value="0" <?php echo $model->getSetting('social_k2_item_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_googleplus0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Google +1 button when the Like button is displayed in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Verb to Display:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_verb_to_display" class="radio">
                    <input type="radio" id="social_k2_item_like_verb_to_displayLike" name="social_k2_item_like_verb_to_display" value="like" <?php echo $model->getSetting('social_k2_item_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_verb_to_displayLike">Like</label>
                    <input type="radio" id="social_k2_item_like_verb_to_displayRec" name="social_k2_item_like_verb_to_display" value="recommend" <?php echo $model->getSetting('social_k2_item_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_verb_to_displayRec">Recommend</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The verb to display in the button in the item view.  Currently only like and recommend are supported">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_color_scheme" class="radio">
                    <input type="radio" id="social_k2_item_like_color_schemeL" name="social_k2_item_like_color_scheme" value="light" <?php echo $model->getSetting('social_k2_item_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_color_schemeL">Light</label>
                    <input type="radio" id="social_k2_item_like_color_schemeD" name="social_k2_item_like_color_scheme" value="dark" <?php echo $model->getSetting('social_k2_item_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Font:</div>
            <div class="config_option">
                <select name="social_k2_item_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_k2_item_like_font') == 'arial' ? 'selected' : ""; ?>>Arial</option>
                    <option value="lucinda grande" <?php echo $model->getSetting('social_k2_item_like_font') == 'lucinda grande' ? 'selected' : ""; ?>>Lucinda Grande</option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_k2_item_like_font') == 'segoe ui' ? 'selected' : ""; ?>>Segoe UI</option>
                    <option value="tahoma" <?php echo $model->getSetting('social_k2_item_like_font') == 'tahoma' ? 'selected' : ""; ?>>Tahoma</option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_k2_item_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>>Trebuchet MS</option>
                    <option value="verdana" <?php echo $model->getSetting('social_k2_item_like_font') == 'verdana' ? 'selected' : ""; ?>>Verdana</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="The font in the item view">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_k2_item_like_width" value="<?php echo $model->getSetting('social_k2_item_like_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the Facebook Like Button frame, in pixels, in the item view. This setting is only used with the 'standard' layout style.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">Blog View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Layout Style:</div>
            <div class="config_option">
                <select name="social_k2_blog_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'standard' ? 'selected' : ""; ?>>standard</option>
                    <option value="box_count" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'box_count' ? 'selected' : ""; ?>>box_count</option>
                    <option value="button_count" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'button_count' ? 'selected' : ""; ?>>button_count</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Determines the size and amount of social context next to the button in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Faces:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_faces" class="radio">
                    <input type="radio" id="social_k2_blog_like_show_faces1" name="social_k2_blog_like_show_faces" value="1" <?php echo $model->getSetting('social_k2_blog_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_faces1">Yes</label>
                    <input type="radio" id="social_k2_blog_like_show_faces0" name="social_k2_blog_like_show_faces" value="0" <?php echo $model->getSetting('social_k2_blog_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_faces0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show profile pictures below the button in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Send Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_send_button" class="radio">
                    <input type="radio" id="social_k2_blog_like_show_send_button1" name="social_k2_blog_like_show_send_button" value="1" <?php echo $model->getSetting('social_k2_blog_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_send_button1">Yes</label>
                    <input type="radio" id="social_k2_blog_like_show_send_button0" name="social_k2_blog_like_show_send_button" value="0" <?php echo $model->getSetting('social_k2_blog_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_send_button0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show send button next to like button in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show LinkedIn Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_linkedin" class="radio">
                    <input type="radio" id="social_k2_blog_like_show_linkedin1" name="social_k2_blog_like_show_linkedin" value="1" <?php echo $model->getSetting('social_k2_blog_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_linkedin1">Yes</label>
                    <input type="radio" id="social_k2_blog_like_show_linkedin0" name="social_k2_blog_like_show_linkedin" value="0" <?php echo $model->getSetting('social_k2_blog_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_linkedin0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show LinkedIn Share buttons when the Like button is displayed in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Twitter Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_twitter" class="radio">
                    <input type="radio" id="social_k2_blog_like_show_twitter1" name="social_k2_blog_like_show_twitter" value="1" <?php echo $model->getSetting('social_k2_blog_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_twitter1">Yes</label>
                    <input type="radio" id="social_k2_blog_like_show_twitter0" name="social_k2_blog_like_show_twitter" value="0" <?php echo $model->getSetting('social_k2_blog_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_twitter0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Twitter Share buttons when the Like button is displayed in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show Google+ Button:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_googleplus" class="radio">
                    <input type="radio" id="social_k2_blog_like_show_googleplus1" name="social_k2_blog_like_show_googleplus" value="1" <?php echo $model->getSetting('social_k2_blog_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_googleplus1">Yes</label>
                    <input type="radio" id="social_k2_blog_like_show_googleplus0" name="social_k2_blog_like_show_googleplus" value="0" <?php echo $model->getSetting('social_k2_blog_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_googleplus0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="Show Google +1 buttons when the Like button is displayed in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Verb to Display:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_verb_to_display" class="radio">
                    <input type="radio" id="social_k2_blog_like_verb_to_displayLike" name="social_k2_blog_like_verb_to_display" value="like" <?php echo $model->getSetting('social_k2_blog_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_verb_to_displayLike">Like</label>
                    <input type="radio" id="social_k2_blog_like_verb_to_displayRec" name="social_k2_blog_like_verb_to_display" value="recommend" <?php echo $model->getSetting('social_k2_blog_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_verb_to_displayRec">Recommend</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The verb to display in the button in blog views.  Currently only like and recommend are supported">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Color Scheme:</div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_color_scheme" class="radio">
                    <input type="radio" id="social_k2_blog_like_color_schemeL" name="social_k2_blog_like_color_scheme" value="light" <?php echo $model->getSetting('social_k2_blog_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_color_schemeL">Light</label>
                    <input type="radio" id="social_k2_blog_like_color_schemeD" name="social_k2_blog_like_color_scheme" value="dark" <?php echo $model->getSetting('social_k2_blog_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_color_schemeD">Dark</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="The color scheme in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Font:</div>
            <div class="config_option">
                <select name="social_k2_blog_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_k2_blog_like_font') == 'arial' ? 'selected' : ""; ?>>Arial</option>
                    <option value="lucinda grande" <?php echo $model->getSetting('social_k2_blog_like_font') == 'lucinda grande' ? 'selected' : ""; ?>>Lucinda Grande</option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_k2_blog_like_font') == 'segoe ui' ? 'selected' : ""; ?>>Segoe UI</option>
                    <option value="tahoma" <?php echo $model->getSetting('social_k2_blog_like_font') == 'tahoma' ? 'selected' : ""; ?>>Tahoma</option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_k2_blog_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>>Trebuchet MS</option>
                    <option value="verdana" <?php echo $model->getSetting('social_k2_blog_like_font') == 'verdana' ? 'selected' : ""; ?>>Verdana</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="The font in blog views">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Width:</div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_like_width" value="<?php echo $model->getSetting('social_k2_blog_like_width') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="The width of the Facebook Like Button frame, in pixels, in blog views. This setting is only used with the 'standard' layout style.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting header">View Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Item View:</div>
            <div class="config_option">
                <?php $socialK2LikeItemView = $model->getSetting('social_k2_like_item_view');?>
                <select name="social_k2_like_item_view">
                    <option value="1" <?php echo ($socialK2LikeItemView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2LikeItemView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2LikeItemView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2LikeItemView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Item view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Category View:</div>
            <div class="config_option">
                <?php $socialK2LikeCategoryView = $model->getSetting('social_k2_like_category_view');?>
                <select name="social_k2_like_category_view">
                    <option value="1" <?php echo ($socialK2LikeCategoryView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2LikeCategoryView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2LikeCategoryView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2LikeCategoryView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Category view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Tag View:</div>
            <div class="config_option">
                <?php $socialK2LikeTagView = $model->getSetting('social_k2_like_tag_view');?>
                <select name="social_k2_like_tag_view">
                    <option value="1" <?php echo ($socialK2LikeTagView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2LikeTagView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2LikeTagView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2LikeTagView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Tag view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Userpage View:</div>
            <div class="config_option">
                <?php $socialK2LikeUserpageView = $model->getSetting('social_k2_like_userpage_view');?>
                <select name="social_k2_like_userpage_view">
                    <option value="1" <?php echo ($socialK2LikeUserpageView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2LikeUserpageView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2LikeUserpageView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2LikeUserpageView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Userpage view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Show in K2 Latest View:</div>
            <div class="config_option">
                <?php $socialK2LikeLatestView = $model->getSetting('social_k2_like_latest_view');?>
                <select name="social_k2_like_latest_view">
                    <option value="1" <?php echo ($socialK2LikeLatestView == '1') ? 'selected' : ""; ?>>Top</option>
                    <option value="2" <?php echo ($socialK2LikeLatestView == '2') ? 'selected' : ""; ?>>Bottom</option>
                    <option value="3" <?php echo ($socialK2LikeLatestView == '3') ? 'selected' : ""; ?>>Both</option>
                    <option value="0" <?php echo ($socialK2LikeLatestView == '0') ? 'selected' : ""; ?>>None</option>
                </select>
            </div>
            <div class="config_description hasTip"
                title="Show in K2 Latest view when settings include the Like button">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <!-- Category -->
        <div class="config_row">
            <div class="config_setting_option header">K2 Category Setting</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting_option">
                <?php $k2catType = $model->getSetting('social_k2_like_cat_include_type'); ?>
                <fieldset id="social_k2_like_cat_include_type" class="radio">
                    <input type="radio" id="social_k2_like_cat_include_type0" name="social_k2_like_cat_include_type" value="0" <?php echo ($k2catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_like_cat_ids', 'none')" />
                    <label for="social_k2_like_cat_include_type0">All</label>
                    <input type="radio" id="social_k2_like_cat_include_type1" name="social_k2_like_cat_include_type" value="1" <?php echo ($k2catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_like_cat_ids', '')" />
                    <label for="social_k2_like_cat_include_type1">Include</label>
                    <input type="radio" id="social_k2_like_cat_include_type2" name="social_k2_like_cat_include_type" value="2" <?php echo ($k2catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_like_cat_ids', '')" />
                    <label for="social_k2_like_cat_include_type2">Exclude</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="All::All categories will have the Like button<br/><br/>
                <strong>Include</strong><br/>Only specified categories will have the Like button<br/><br/>
                <strong>Exclude</strong><br/>All categories, except those specified, will have the Like button.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row" id="k2_like_cat_ids" style="display:<?php echo ($k2catType == "0" ? 'none' : ''); ?>">
<?php
            $k2catids = $model->getSetting('social_k2_like_cat_ids');
            $k2categories = unserialize($k2catids);

            $query = "SELECT `id`, `name` FROM #__k2_categories";
            $db = JFactory::getDBO();
            $db->setQuery($query);
            $k2cats = $db->loadAssocList();
            $attribs = 'multiple="multiple"';
            echo '<td>' . JHTML::_('select.genericlist', $k2cats, 'social_k2_like_cat_ids[]', $attribs, 'id', 'name', $k2categories, 'social_k2_like_cat_ids') . '</td>';
?>
        <div style="clear:both"></div>
        </div>
        <!-- End Categories -->
        <div class="config_row">
            <div class="config_setting header">K2 Item Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Include:</div>
            <div class="config_option">
                <input type="text" name="social_k2_like_item_include_ids" value="<?php echo $model->getSetting('social_k2_like_item_include_ids'); ?>" size="20">
                </div>
                <div class="config_description hasTip"
                    title="Comma-separated list of K2 Item IDs to specifically include">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Exclude:</div>
                <div class="config_option">
                    <input type="text" name="social_k2_like_item_exclude_ids" value="<?php echo $model->getSetting('social_k2_like_item_exclude_ids'); ?>" size="20">
                </div>
                <div class="config_description hasTip"
                    title="Comma-separated list of K2 Item IDs to specifically exclude">Info
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
<?php
    echo $pane->endPanel();
    }
    echo $pane->startPanel('Notifications/Analytics', 'social_notifications');
?>
        <div>
            <div class="config_row">
                <div class="config_setting header">Setting</div>
                <div class="config_option header">Options</div>
                <div class="config_description header">Description</div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Send Email Notification on New Comments</div>
                <div class="config_option">
                    <fieldset id="social_notification_comment_enabled" class="radio">
                        <input type="radio" id="social_notification_comment_enabled1" name="social_notification_comment_enabled" value="1" <?php echo $model->getSetting('social_notification_comment_enabled') ? 'checked="checked"' : ""; ?> />
                        <label for="social_notification_comment_enabled1">Yes</label>
                        <input type="radio" id="social_notification_comment_enabled0" name="social_notification_comment_enabled" value="0" <?php echo $model->getSetting('social_notification_comment_enabled') ? '""' : 'checked="checked"'; ?> />
                        <label for="social_notification_comment_enabled0">No</label>
                    </fieldset>
                </div>
                <div class="config_description hasTip"
                    title="When enabled, an email will be sent to the user(s) below whenever a new Facebook Comment is added on the site.">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Send Email Notification on New Likes</div>
                <div class="config_option">
                    <fieldset id="social_notification_like_enabled" class="radio">
                        <input type="radio" id="social_notification_like_enabled1" name="social_notification_like_enabled" value="1" <?php echo $model->getSetting('social_notification_like_enabled') ? 'checked="checked"' : ""; ?> />
                        <label for="social_notification_like_enabled1">Yes</label>
                        <input type="radio" id="social_notification_like_enabled0" name="social_notification_like_enabled" value="0" <?php echo $model->getSetting('social_notification_like_enabled') ? '""' : 'checked="checked"'; ?> />
                        <label for="social_notification_like_enabled0">No</label>
                    </fieldset>
                </div>
                <div class="config_description hasTip"
                    title="When enabled, an email will be sent to the user(s) below whenever the Like button is clicked on the site.">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Addresses to Notify</div>
                <div class="config_option">
                    <textarea name="social_notification_email_address" rows="3" cols="30"><?php echo $model->getSetting('social_notification_email_address') ?></textarea><br/>
                </div>
                <div class="config_description hasTip"
                    title="Comma separated email addresses that should be notified for the above events.">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Add Google Analytics Tracking for New Likes</div>
                <div class="config_option">
                    <fieldset id="social_notification_google_analytics" class="radio">
                        <input type="radio" id="social_notification_google_analytics1" name="social_notification_google_analytics" value="1" <?php echo $model->getSetting('social_notification_google_analytics') ? 'checked="checked"' : ""; ?> />
                        <label for="social_notification_google_analytics1">Yes</label>
                        <input type="radio" id="social_notification_google_analytics0" name="social_notification_google_analytics" value="0" <?php echo $model->getSetting('social_notification_google_analytics') ? '""' : 'checked="checked"'; ?> />
                        <label for="social_notification_google_analytics0">No</label>
                    </fieldset>
                </div>
                <div class="config_description hasTip"
                    title="When enabled, add google analytics tracking for new like button clicks. Google Analytics must already be set up on your site.">Info
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel('Open Graph', 'social_graph');
?>
    <div>
        <div class="config_row">
            <div class="config_setting header">Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Enabled</div>
            <div class="config_option">
                <fieldset id="social_graph_enabled" class="radio">
                    <input type="radio" id="social_graph_enabled1" name="social_graph_enabled" value="1" <?php echo $model->getSetting('social_graph_enabled') ? 'checked="checked"' : ""; ?> />
                    <label for="social_graph_enabled1">Yes</label>
                    <input type="radio" id="social_graph_enabled0" name="social_graph_enabled" value="0" <?php echo $model->getSetting('social_graph_enabled') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_graph_enabled0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="When enabled, Open Graph protocol meta tags will be added to your page.">Info
            </div>
            <div class="config_description2">
                See <a href="http://developers.facebook.com/docs/opengraph/" target="_blank">Facebook Open Graph documentation</a> for complete details.
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Use first image in article for default</div>
            <div class="config_option">
                <fieldset id="social_graph_first_image" class="radio">
                    <input type="radio" id="social_graph_first_image1" name="social_graph_first_image" value="1" <?php echo $model->getSetting('social_graph_first_image') ? 'checked="checked"' : ""; ?> />
                    <label for="social_graph_first_image1">Yes</label>
                    <input type="radio" id="social_graph_first_image0" name="social_graph_first_image" value="0" <?php echo $model->getSetting('social_graph_first_image') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_graph_first_image0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="When enabled, the first image in your article will be used for the og:image property.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Use beginning text in article for default</div>
            <div class="config_option">
                <fieldset id="social_graph_first_text" class="radio">
                    <input type="radio" id="social_graph_first_text1" name="social_graph_first_text" value="1" <?php echo $model->getSetting('social_graph_first_text') ? 'checked="checked"' : ""; ?> />
                    <label for="social_graph_first_text1">Yes</label>
                    <input type="radio" id="social_graph_first_text0" name="social_graph_first_text" value="0" <?php echo $model->getSetting('social_graph_first_text') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_graph_first_text0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="When enabled, the first characters in your article text will be used for the og:description property.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Length of beginning text to use</div>
            <div class="config_option">
                <input type="text" name="social_graph_first_text_length"  value="<?php echo $model->getSetting('social_graph_first_text_length') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="Number of characters to use when beginning text is used for og:description property">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting" style="width:100px">Defaults</div>
            <div class="config_option" style="width:375px">
                <textarea rows="15" cols="43" name="social_graph_fields"><?php echo $model->getSetting('social_graph_fields') ?></textarea>
            </div>
            <div class="config_description hasTip"
                title="Open Graph fields will be added to your page in the following order:
                <ol>
                    <li>All {SCOpenGraph} tags will be added first. Use these tags to specify values for individual pages.</li>
                    <li>If enabled above, the first image in your article will be used as a default image.</li>
                    <li>If enabled above, the first 100 characters of your article text will be used as a default description.</li>
                    <li>
                        The default fields specified to the left will be added to all of your site pages. They will not override any {SCOpenGraph} tags.
                        This should be a list of Open Graph fields and values, separated by carriage returns. See example of a valid entry below.
                    </li>
                    <li>
                        If not already added, Description, URL and Title will be added automatically by JFBConnect. <br/>
                        <strong><em>(Note: We recommend these automatic tags so that each page uses its own title, description
                                and URL instead of a generic value across all pages.)</em></strong>
                    </li>
                </ol>">Info
            </div><br/><br/>
            <div class="config_description" style="min-width:300px; width:600px; margin-left:10px">
                Examples of supported fields are title, type, image, url, site_name, admins, app_id, description, latitude, etc.
                A full list can be found in the <a href="http://developers.facebook.com/docs/opengraph" target="_blank">Facebook OpenGraph documentation</a>
                <br/><br/>Example of valid entry:<br/><em>
                    title=SourceCoast Web Development<br/>
                    type=company<br/>
                    site_name=SourceCoast<br/>
                    description=Joomla Facebook Connect integration, based in Austin, TX<br/></em>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div><strong>Want to see what your page looks like to Facebook?</strong> Use their <a href="http://developers.facebook.com/tools/debug" target="_blank">Linter</a>.</div>

<?php
    echo $pane->endPanel();
    echo $pane->startPanel('Misc', 'social_misc');
?>
    <div>
        <div class="config_row">
            <div class="config_setting header">Social Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Social Tag Admin Key</div>
            <div class="config_option">
                <input type="text" name="social_tag_admin_key"  value="<?php echo $model->getSetting('social_tag_admin_key') ?>" size="20">
            </div>
            <div class="config_description hasTip"
                title="Key to prevent user-entered social tags from rendering. When this value is set, you must add key=XXX when adding your tags. Ex. {JFBCLike key=1234}<br/><br/>
                This is useful if you have forums, comments or other user-entered text available on your site.<br/><br/>
                Leave blank to allow users to enter social tags, or if you don't have any user-generated content on your site.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Alpha User Points Integration</div>
            <div class="config_option">
                <fieldset id="social_alphauserpoints_enabled" class="radio">
                                    <input type="radio" id="social_alphauserpoints_enabled1" name="social_alphauserpoints_enabled" value="1" <?php echo $model->getSetting('social_alphauserpoints_enabled') ? 'checked="checked"' : ""; ?> />
                                    <label for="social_alphauserpoints_enabled1">Enabled</label>
                                    <input type="radio" id="social_alphauserpoints_enabled0" name="social_alphauserpoints_enabled" value="0" <?php echo $model->getSetting('social_alphauserpoints_enabled') ? '""' : 'checked="checked"'; ?> />
                                    <label for="social_alphauserpoints_enabled0">Disabled</label>
                                </fieldset>
            </div>
            <div class="config_description hasTip"
                title="When enabled, and Alpha User Points is installed, allows the rewarding of 'points' to users for activities such as Like'ing and commenting on content or for sending Facebook Requests to their friends.
                <br/><br/>The AUP/JFBConnect 'rules' from SourceCoast.com need to be installed and configured in AUP for points to be properly rewarded.">Info
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel('Examples', 'social_examples');
?>
    <p>The JFBCSystem plugin allows you to embed the social modules into your page content. After enabling the plugin in your
        backend, add the following tags into any article, module or template files. The parameters should be case-insensitive.</p>
    <p>Please see <a href="http://www.sourcecoast.com/jfbconnect/docs/configuration-guide" target="_blank">our configuration guide</a> for the most up-to-date options available.</p>

    <p> </p>
    <h3>Comments:</h3>
    Example: {JFBCComments}<br/>
    Example: {JFBCComments href=http://www.sourcecoast.com width=550 num_posts=10 colorscheme=dark mobile=false}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">The URL for the comments box</td>
        </tr>
        <tr>
            <td class="odd">width</td>
            <td class="odd">Integer Value</td>
            <td class="odd">500</td>
            <td class="odd">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="even">num_posts</td>
            <td class="even">Integer Value</td>
            <td class="even">2</td>
            <td class="even">The number of comments to display</td>
        </tr>
        <tr>
            <td class="odd">colorscheme</td>
            <td class="odd">light, dark</td>
            <td class="odd">light</td>
            <td class="odd">Color scheme to use, provided by Facebook</td>
        </tr>
        <tr>
            <td class="even">mobile</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Whether to show the mobile-optimized version</td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Comments Count:</h3>
    Example: {JFBCCommentsCount}<br/>
    Example: {JFBCCommentsCount href=http://www.sourcecoast.com}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">The URL for the comments count box</td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Fan:</h3>
    Example: {JFBCFan}<br/>
    Example: {JFBCFan height=200 width=200 colorscheme=light href=http://www.sourcecoast.com show_faces=true stream=false header=true border_color=blue force_wall=false}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">height</td>
            <td class="even">Integer Value</td>
            <td class="even">200</td>
            <td class="even">The height of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="odd">width</td>
            <td class="odd">Integer Value</td>
            <td class="odd">292</td>
            <td class="odd">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="even">colorscheme</td>
            <td class="even">light, dark</td>
            <td class="even">light</td>
            <td class="even">Color scheme to use, provided by Facebook</td>
        </tr>
        <tr>
            <td class="odd">href</td>
            <td class="odd">A valid URL</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">The URL of the Facebook Page for this Like box</td>
        </tr>
        <tr>
            <td class="even">show_faces</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Show profile pictures of users who are fans</td>
        </tr>
        <tr>
            <td class="odd">stream</td>
            <td class="odd">true, 1, false, 0</td>
            <td class="odd">true</td>
            <td class="odd">Show the profile stream for the public profile</td>
        </tr>
        <tr>
            <td class="even">header</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Show the Find Us on Facebook bar at top. Only shown when either stream or connections are
                present
            </td>
        </tr>
        <tr>
            <td class="odd">border_color</td>
            <td class="odd">black, red, blue, etc.</td>
            <td class="odd">black</td>
            <td class="odd">The border color</td>
        </tr>
        <tr>
            <td class="even">force_wall</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">false</td>
            <td class="even">For Places, specifies whether the stream contains posts from the Place's wall or just checkins from friends</td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Feed:</h3>
    Example: {JFBCFeed}<br/>
    Example: {JFBCFeed site=http://www.sourcecoast.com height=300 width=300 colorscheme=light font=verdana border_color=blue recommendations=true header=false link_target=_top}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">site</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to default to the domain the plugin is on</td>
            <td class="even">The domain for which to show recommendations</td>
        </tr>
        <tr>
            <td class="odd">height</td>
            <td class="odd">Integer Value</td>
            <td class="odd">300</td>
            <td class="odd">The height of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="even">width</td>
            <td class="even">Integer Value</td>
            <td class="even">300</td>
            <td class="even">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="odd">colorscheme</td>
            <td class="odd">light, dark</td>
            <td class="odd">light</td>
            <td class="odd">The color scheme</td>
        </tr>
        <tr>
            <td class="even">font</td>
            <td class="even">arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana</td>
            <td class="even">arial</td>
            <td class="even">The font</td>
        </tr>
        <tr>
            <td class="odd">border_color</td>
            <td class="odd">black, red, blue, etc.</td>
            <td class="odd">black</td>
            <td class="odd">The border color</td>
        </tr>
        <tr>
            <td class="even">recommendations</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Show Recommendations</td>
        </tr>
        <tr>
            <td class="odd">header</td>
            <td class="odd">true, 1, false 0</td>
            <td class="odd">true</td>
            <td class="odd">Show the Facebook header</td>
        </tr>
        <tr>
            <td class="even">link_target</td>
            <td class="even">_blank, _top, _parent</td>
            <td class="even">_blank</td>
            <td class="even">The context in which content links are opened</td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Friends:</h3>
    Example: {JFBCFriends}<br/>
    Example: {JFBCFriends href=http://www.sourcecoast.com max_rows=5 width=400 colorscheme=dark size=small}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">If you want to display friends who have liked your page, specify the URL of the page here</td>
        </tr>
        <tr>
            <td class="odd">max_rows</td>
            <td class="odd">Integer Value</td>
            <td class="odd">1</td>
            <td class="odd">The maximum number of rows of profile pictures to show</td>
        </tr>
        <tr>
            <td class="even">width</td>
            <td class="even">Integer Value</td>
            <td class="even">200</td>
            <td class="even">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="odd">colorscheme</td>
            <td class="odd">light, dark</td>
            <td class="odd">light</td>
            <td class="odd">Color scheme to use, provided by Facebook</td>
        </tr>
        <tr>
            <td class="even">size</td>
            <td class="even">small, large</td>
            <td class="even">small</td>
            <td class="even">Determines the size of the images and social context in the facepile</td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Like:</h3>
    Example: {JFBCLike}<br/>
    Example: {JFBCLike href=http://www.sourcecoast.com layout=standard show_faces=true show_send_button=true width=300 action=like font=verdana colorscheme=light}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to default to current page</td>
            <td class="even">The URL to like</td>
        </tr>
        <tr>
            <td class="odd">layout</td>
            <td class="odd">standard, box_count, button_count</td>
            <td class="odd">standard</td>
            <td class="odd">Determines the size and amount of social context next to the button</td>
        </tr>
        <tr>
            <td class="even">show_faces</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Show profile pictures below the button.<br/>This only works with Standard Layout Style</td>
        </tr>
        <tr>
            <td class="odd">show_send_button</td>
            <td class="odd">true, 1, false, 0</td>
            <td class="odd">true</td>
            <td class="odd">Show send button</td>
        </tr>
        <tr>
            <td class="even">width</td>
            <td class="even">Integer Value</td>
            <td class="even">450</td>
            <td class="even">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="odd">action</td>
            <td class="odd">like, recommend</td>
            <td class="odd">like</td>
            <td class="odd">The verb to display in the button. Currently only like and recommend are supported</td>
        </tr>
        <tr>
            <td class="even">font</td>
            <td class="even">arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana</td>
            <td class="even">arial</td>
            <td class="even">The font</td>
        </tr>
        <tr>
            <td class="odd">colorscheme</td>
            <td class="odd">light, dark</td>
            <td class="odd">light</td>
            <td class="odd">The color scheme</td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Send:</h3>
    Example: {JFBCSend}<br/>
    Example: {JFBCSend href=http://www.sourcecoast.com font=verdana colorscheme=light ref=homepage}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to default to current page</td>
            <td class="even">The URL to like</td>
        </tr>
        <tr>
            <td class="odd">font</td>
            <td class="odd">arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana</td>
            <td class="odd">arial</td>
            <td class="odd">The font</td>
        </tr>
        <tr>
            <td class="even">colorscheme</td>
            <td class="even">light, dark</td>
            <td class="even">light</td>
            <td class="even">The color scheme</td>
        </tr>
        <tr>
            <td class="odd">ref</td>
            <td class="odd">Must be less than 50 characters and can contain alphanumeric characters and some punctuation
                (currently +/=-.:_)
            </td>
            <td class="odd">homepage</td>
            <td class="odd">A label for tracking referrals. Specifying the ref attribute will add the 'fb_ref' parameter to
                the referrer URL when a user clicks a link from the plugin.
            </td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>LiveStream:</h3>
    Example: {JFBCLiveStream}<br/>
    Example: {JFBCLiveStream width=400 height=500 event_app_id=123456789101112 via_url=http://www.sourcecoast.com always_post_to_friends=false}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">width</td>
            <td class="even">Integer value</td>
            <td class="even">400</td>
            <td class="even">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="odd">height</td>
            <td class="odd">Integer value</td>
            <td class="odd">500</td>
            <td class="odd">The height of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="even">xid</td>
            <td class="even">Unique alphanumeric value</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">If you have multiple live stream boxes on the same page, specify a unique xid for each</td>
        </tr>
        <tr>
            <td class="odd">event_app_id</td>
            <td class="odd">Application ID or API key found in your FB app <a href="http://www.facebook.com/developers/">here</a>
            </td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Your Facebook application ID or API key</td>
        </tr>
        <tr>
            <td class="even">via_url</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to use your Connect URL</td>
            <td class="even">The URL that users are redirected to when they click on your app name on a status</td>
        </tr>
        <tr>
            <td class="odd">always_post_to_friends</td>
            <td class="odd">true, 1, false, 0</td>
            <td class="odd">false</td>
            <td class="odd">If set, all user posts will always go to their profile. This option should only be used when
                users' posts are likely to make sense outside of the context of this event
            </td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Recommendations:</h3>
    Example: {JFBCRecommendations}<br/>
    Example: {JFBCRecommendations site=http://www.sourcecoast.com width=350 height=350 colorscheme=light header=false font=verdana border_color=blue link_target=_top}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">site</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to default to the domain the plugin is on</td>
            <td class="even">The domain for which to show recommendations</td>
        </tr>
        <tr>
            <td class="odd">width</td>
            <td class="odd">Integer value</td>
            <td class="odd">300</td>
            <td class="odd">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="even">height</td>
            <td class="even">Integer value</td>
            <td class="even">300</td>
            <td class="even">The height of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="odd">colorscheme</td>
            <td class="odd">light, dark</td>
            <td class="odd">light</td>
            <td class="odd">The color scheme</td>
        </tr>
        <tr>
            <td class="even">header</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Show the Facebook header</td>
        </tr>
        <tr>
            <td class="odd">font</td>
            <td class="odd">arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana</td>
            <td class="odd">arial</td>
            <td class="odd">The font</td>
        </tr>
        <tr>
            <td class="even">border_color</td>
            <td class="even">black, red, blue, etc</td>
            <td class="even">black</td>
            <td class="even">The border color</td>
        </tr>
        <tr>
            <td class="odd">link_target</td>
            <td class="odd">_blank, _top, _parent</td>
            <td class="odd">_blank</td>
            <td class="odd">The context in which content links are opened</td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Subscribe:</h3>
    Example: {JFBCSubscribe}<br/>
    Example: {JFBCSubscribe href=https://www.facebook.com/zuck layout=standard show_faces=true width=300 action=like font=verdana colorscheme=light}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">The profile URL of the user to subscribe to</td>
        </tr>
        <tr>
            <td class="odd">layout</td>
            <td class="odd">standard, box_count, button_count</td>
            <td class="odd">standard</td>
            <td class="odd">Determines the size and amount of social context next to the button</td>
        </tr>
        <tr>
            <td class="even">show_faces</td>
            <td class="even">true, 1, false, 0</td>
            <td class="even">true</td>
            <td class="even">Show profile pictures below the button.<br/>This only works with Standard Layout Style</td>
        </tr>
        <tr>
            <td class="odd">width</td>
            <td class="odd">Integer Value</td>
            <td class="odd">450</td>
            <td class="odd">The width of the frame, in pixels</td>
        </tr>
        <tr>
            <td class="even">font</td>
            <td class="even">arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana</td>
            <td class="even">arial</td>
            <td class="even">The font</td>
        </tr>
        <tr>
            <td class="odd">colorscheme</td>
            <td class="odd">light, dark</td>
            <td class="odd">light</td>
            <td class="odd">The color scheme</td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Request:</h3>
    Example: {JFBCRequest request_id=1 link_image=http://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.png}<br/>
    Example: {JFBCRequest request_id=1 link_text=Invite Friends}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">request_id</td>
            <td class="even">Valid Request ID</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">An ID for a request (which has already been set up and published in the administrator area)
            </td>
        </tr>
        <tr>
            <td class="odd">link_text</td>
            <td class="odd">Alphanumeric value</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Use this field for a simple text link to open the request window or use the link_image field.
            </td>
        </tr>
        <tr>
            <td class="even">link_image</td>
            <td class="even">Valid image URL</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Use this field for an image link to open the request window or use the link_text field. This
                should be a URL to the image.
            </td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Login:</h3>
    Example: {JFBCLogin}<br/>
    Example: {JFBCLogin size=medium logout=true}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">size</td>
            <td class="even">small, medium, large, xlarge</td>
            <td class="even">medium</td>
            <td class="even">Size of the Login with Facebook button</td>
        </tr>
        <tr>
            <td class="odd">logout</td>
            <td class="odd">true, 1, false, 0</td>
            <td class="odd">false</td>
            <td class="odd">True or 1 to show the logout button when the user is logged in. <br/>False or 0 to hide the
                button.
            </td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Graph:</h3>
    See <a href="http://developers.facebook.com/docs/opengraph/" target="_blank">Facebook Open Graph documentation</a> for a full list of available fields.<br/><br/>
    Example: {SCOpenGraph url=http://www.sourcecoast.com}<br/>
    Example: {SCOpenGraph image=http://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.jpg}<br/>
    Example: {SCOpenGraph description=Facebook connect integration for Joomla! Let users register and log into your site with their Facebook credentials.}
    <br/><br/><strong><em>Note: Each Graph tag must only contain one property value. This is different than other JFBConnect tags which allow for multiple fields to be defined within the same {}.</em></strong><br/>
    <br/>
    <table class="options">
        <tr>
            <th>Property</th>
            <th>Example</th>
        </tr>
        <tr>
            <td class="even">title</td>
            <td class="even">title=JFBConnect</td>
        </tr>
        <tr>
            <td class="odd">type</td>
            <td class="odd">type=company</td>
        </tr>
        <tr>
            <td class="even">url</td>
            <td class="even">url=http://joomla-facebook.com</td>
        </tr>
        <tr>
            <td class="odd">image</td>
            <td class="odd">image=http://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.jpg</td>
        </tr>
        <tr>
            <td class="even">site_name</td>
            <td class="even">site_name=SourceCoast</td>
        </tr>
        <tr>
            <td class="odd">description</td>
            <td class="odd">description=Joomla Facebook Connect integration, payment systems, and custom Joomla development
                based in Austin, TX
            </td>
        </tr>
    </table>
    <p></p>
    <h3>Twitter Share Button:</h3>
    Example: {SCTwitterShare}<br/>
    Example: {SCTwitterShare href=http://www.sourcecoast.com data_count=horizontal}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to default to current page</td>
            <td class="even">The URL to share</td>
        </tr>
        <tr>
            <td class="odd">data_count</td>
            <td class="odd">horizontal, vertical, none</td>
            <td class="odd">horizontal</td>
            <td class="odd">Determines the layout and amount of social context next to the button</td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>Google +1 Button:</h3>
    Example: {SCGooglePlusOne}<br/>
    Example: {SCGooglePlusOne href=http://www.sourcecoast.com annotation=inline size=standard}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em> to default to current page</td>
            <td class="even">The URL to share</td>
        </tr>
        <tr>
            <td class="odd">annotation</td>
            <td class="odd">inline, bubble, none</td>
            <td class="odd">inline</td>
            <td class="odd">Determines the amount of social context next to the button</td>
        </tr>
        <tr>
            <td class="even">size</td>
            <td class="even">small, medium, standard, tall</td>
            <td class="even">standard</td>
            <td class="even">Determines the layout of the button</td>
        </tr>
        <tr>
            <td class="odd">key</td>
            <td class="odd">The render key value you have set in the "Misc" tab.</td>
            <td class="odd"><em>Blank</em></td>
            <td class="odd">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
    <p></p>
    <h3>LinkedIn Share Button:</h3>
    Example: {JLinkedShare}<br/>
    Example: {JLinkedShare href=http://www.sourcecoast.com/jlinked/ counter=top}<br/>
    <br/>
    <table class="options">
        <tr>
            <th>Parameter</th>
            <th>Options</th>
            <th>Default (if not specified)</th>
            <th>Description</th>
        </tr>
        <tr>
            <td class="even">href</td>
            <td class="even">A valid URL</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Defaults to current page. If you want to share a specific URL, specify it here.</td>
        </tr>
        <tr>
            <td class="odd">counter</td>
            <td class="odd">top, right, no_count</td>
            <td class="odd">no_count</td>
            <td class="odd">How to display the share button</td>
        </tr>
        <tr>
            <td class="even">key</td>
            <td class="even">The render key value you have set in the "Misc" tab.</td>
            <td class="even"><em>Blank</em></td>
            <td class="even">Key to prevent user-entered social tags from rendering.</td>
        </tr>
    </table>
<?php
    echo $pane->endPanel();
    echo $pane->endPane();
?>

    <input type="hidden" name="option" value="com_jfbconnect" />
    <input type="hidden" name="controller" value="social" />
    <input type="hidden" name="cid[]" value="0" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_('form.token'); ?>

</form>
