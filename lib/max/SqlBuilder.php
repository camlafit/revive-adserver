<?php

/*
+---------------------------------------------------------------------------+
| Openads v2.3                                                              |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

require_once MAX_PATH . '/lib/max/Delivery/common.php';

/**
 * @package    MaxDal
 * @author     Scott Switzer <scott@m3.net>
 */
class SqlBuilder
{
    // +---------------------------------------+
    // | Relational/constraint methods         |
    // |                                       |
    // | for entity builders                   |
    // +---------------------------------------+

//  not used anywhere
#    function _addZeroStats(&$aRow, $id)
#    {
#        if (empty($aRow['sum_requests'])) $aRow['sum_requests'] = 0;
#        if (empty($aRow['sum_views'])) $aRow['sum_views'] = 0;
#        if (empty($aRow['sum_clicks'])) $aRow['sum_clicks'] = 0;
#        if (empty($aRow['sum_conversions'])) $aRow['sum_conversions'] = 0;
#    }

    /**
     * Returns the fields given the particular entity.
     *
     * @param string $entity
     * @param array $aParams
     * @param boolean $allFields
     * @return array
     */
    function _getColumns($entity, $aParams, $allFields)
    {
        $aColumns = array();
        switch ($entity) {

        case 'ad' :
            $aColumns += array('d.bannerid' => 'ad_id', 'd.campaignid' => 'placement_id', 'd.active' => 'active', 'd.description' => 'name', 'd.storagetype' => 'type');
            if ($allFields) $aColumns += array('d.contenttype' => 'contenttype', 'd.pluginversion' => 'pluginversion', 'd.filename' => 'filename', 'd.imageurl' => 'imageurl', 'd.htmltemplate' => 'htmltemplate', 'd.htmlcache' => 'htmlcache', 'd.width' => 'width', 'd.height' => 'height', 'd.weight' => 'weight', 'd.seq' => 'seq', 'd.target' => 'target', 'd.url' => 'url', 'd.alt' => 'alt', 'd.status' => 'status', 'd.bannertext' => 'bannertext', 'd.autohtml' => 'autohtml', 'd.adserver' => 'adserver', 'd.block' => 'block', 'd.capping' => 'capping', 'd.session_capping' => 'session_capping', 'd.compiledlimitation' => 'compiledlimitation', 'd.append' => 'append', 'd.appendtype' => 'appendtype', 'd.bannertype' => 'bannertype', 'd.alt_filename' => 'alt_filename', 'd.alt_imageurl' => 'alt_imageurl', 'd.alt_contenttype' => 'alt_contenttype', 'd.comments' => 'comments', 'd.parameters' => 'parameters', 'd.transparent' => 'transparent');
            break;

        case 'advertiser' :
            $aColumns += array('a.clientid' => 'advertiser_id', 'a.agencyid' => 'agency_id', 'a.clientname' => 'name');
            if ($allFields) $aColumns += array('a.contact' => 'contact', 'a.email' => 'email', 'a.clientusername' => 'username', 'a.clientpassword' => 'password', 'a.permissions' => 'permissions', 'a.language' => 'language', 'a.report' => 'report', 'a.reportinterval' => 'report_interval', 'a.reportlastdate' => 'report_last_date', 'a.reportdeactivate' => 'report_deactivate');
            break;

        case 'ad_category_assoc' :
            $aColumns += array('ac.ad_category_assoc_id' => 'ad_category_assoc_id', 'ac.ad_id' => 'ad_id', 'ac.category_id' => 'category_id');
            break;

        case 'ad_zone_assoc' :
            $aColumns += array('az.ad_zone_assoc_id' => 'ad_zone_assoc_id', 'az.ad_id' => 'ad_id', 'az.zone_id' => 'zone_id', 'az.priority' => 'priority');
            break;

        case 'agency' :
            $aColumns += array('g.agencyid' => 'agency_id', 'g.name' => 'name', 'g.active' => 'active');
            if ($allFields) $aColumns += array(
                'g.contact' => 'contact',
                'g.email' => 'email',
                'g.username' => 'username',
                'g.password' => 'password',
                'g.permissions' => 'permissions',
                'g.language' => 'language',
                'g.logout_url' => 'logout_url'
                );
            break;

        case 'category' :
            $aColumns += array('cat.category_id' => 'category_id', 'cat.name' => 'name');
            break;

        case 'channel' :
            $aColumns += array('ch.channelid' => 'channel_id', 'ch.agencyid' => 'agency_id', 'ch.affiliateid' => 'publisher_id', 'ch.name' => 'name');
            if ($allFields) $aColumns += array('ch.description' => 'description', 'ch.compiledlimitation' => 'compiledlimitation', 'ch.active' => 'active', 'ch.comments' => 'comments');
            break;

        case 'channel_limitation' :
            $aColumns += array('chl.logical' => 'logical', 'chl.type' => 'type', 'chl.comparison' => 'comparison', 'chl.data' => 'data', 'chl.executionorder' => 'executionorder');
            break;
        case 'image' :
            $aColumns += array('i.filename' => 'file_name');
            if ($allFields) $aColumns += array('i.t_stamp' => 't_stamp', 'i.contents' => 'contents');
            break;

        case 'limitation' :
            $aColumns += array('l.bannerid' => 'ad_id', 'l.logical' => 'logical', 'l.type' => 'type', 'l.comparison' => 'comparison', 'l.data' => 'data', 'l.executionorder' => 'executionorder');
            break;

        case 'placement' :
            $aColumns += array('m.clientid' => 'advertiser_id', 'm.campaignid' => 'placement_id', 'm.campaignname' => 'name', 'm.active' => 'active', 'm.anonymous' => 'anonymous');
            if ($allFields) $aColumns += array('m.views' => 'views', 'm.clicks' => 'clicks', 'm.conversions' => 'conversions', 'm.expire' => 'expire', 'm.activate' => 'activate', 'm.priority' => 'priority', 'm.weight' => 'weight', 'm.target_impression' => 'target_impression', 'm.target_click' => 'target_click', 'm.target_conversion' => 'target_conversion', 'm.anonymous' => 'anonymous');
            break;

        case 'placement_tracker' :
            $aColumns += array('mt.campaign_trackerid' => 'placement_tracker_id', 'mt.campaignid' => 'placement_id', 'mt.trackerid' => 'tracker_id');
            if ($allFields) $aColumns += array('mt.status' => 'status', 'mt.viewwindow' => 'view_window', 'mt.clickwindow' => 'click_window');
            break;

        case 'publisher' :
            $aColumns += array('p.affiliateid' => 'publisher_id', 'p.agencyid' => 'agency_id', 'p.name' => 'name');
            if ($allFields) $aColumns += array('p.mnemonic' => 'mnemonic', 'p.contact' => 'contact', 'p.email' => 'email', 'p.website' => 'website', 'p.username' => 'username', 'p.password' => 'password', 'p.permissions' => 'permissions', 'p.language' => 'language', 'p.publiczones' => 'publiczones');
            break;

        case 'stats' :
            $aColumns += array('s.day' => 'day', 's.hour' => 'hour', 'SUM(s.requests)' => 'sum_requests', 'SUM(s.impressions)' => 'sum_views', 'SUM(s.clicks)' => 'sum_clicks', 'SUM(s.conversions)' => 'sum_conversions');
            break;

        case 'stats_by_entity' :
            if (isset($aParams['include']) && is_array($aParams['include'])) {
                if (array_search('advertiser_id', $aParams['include']) !== false) $aColumns += array('m.clientid' => 'advertiser_id');
                if (array_search('placement_id', $aParams['include']) !== false)  $aColumns += array('d.campaignid' => 'placement_id');
                if (array_search('publisher_id', $aParams['include']) !== false)  $aColumns += array('z.affiliateid' => 'publisher_id');
            }
            $aColumns += array("CONCAT(s.ad_id, '_', s.zone_id)" => 'pkey', 's.ad_id' => 'ad_id', 's.zone_id' => 'zone_id') + SqlBuilder::_getColumns('stats_common', $aParams, $allFields);

            if (isset($aParams['add_columns']) && is_array($aParams['add_columns'])) {
                $aColumns += $aParams['add_columns'];
            }

            // Remove unused columns to avoid implicit group by
            if (isset($aParams['exclude']) && is_array($aParams['exclude'])) {
                if (array_search('ad_id', $aParams['exclude']) !== false) {
                    unset($aColumns["CONCAT(s.ad_id, '_', s.zone_id)"]);
                    unset($aColumns['s.ad_id']);
                    if (array_search('zone_id', $aParams['exclude']) !== false) {
                        unset($aColumns['s.zone_id']);
                        if (count($aParams['include'])) {
                            $tr = array(
                                'placement_id'  => 'd.campaignid',
                                'advertiser_id' => 'm.clientid',
                                'publisher_id' => 'z.affiliateid'
                            );
                            $aColumns["CONCAT(".strtr(join(", '_', ", $aParams['include']), $tr).")"] = 'pkey';
                        } else {
                            $aColumns["(0)"] = 'pkey';
                        }
                    } else {
                        $aColumns["(s.zone_id)"] = 'pkey';
                    }
                } elseif (array_search('zone_id', $aParams['exclude']) !== false) {
                    unset($aColumns["CONCAT(s.ad_id, '_', s.zone_id)"]);
                    unset($aColumns['s.zone_id']);
                    $aColumns["(s.ad_id)"] = 'pkey';
                }
            }
            break;

        case 'history_span' :
            if (isset($aParams['custom_columns']) && is_array($aParams['custom_columns'])) {
                $aColumns += $aParams['custom_columns'];
            } else {
                $aColumns += array('MIN(s.day)' => 'start_date');
            }
            break;

        case 'history_day' :
            $aColumns += array('s.day' => 'day', "DATE_FORMAT(s.day, '{$GLOBALS['date_format']}')" => 'date_f') + SqlBuilder::_getColumns('stats_common', $aParams, $allFields);
            break;

        case 'history_month' :
            $aColumns += array("DATE_FORMAT(s.day, '%Y-%m')" => 'month', "DATE_FORMAT(s.day, '{$GLOBALS['month_format']}')" => 'date_f') + SqlBuilder::_getColumns('stats_common', $aParams, $allFields);
            break;

        case 'history_dow' :
            $aColumns += array("(DAYOFWEEK(s.day) - 1)" => 'dow') + SqlBuilder::_getColumns('stats_common', $aParams, $allFields);
            break;

        case 'history_hour' :
            $aColumns += array("s.hour" => 'hour') + SqlBuilder::_getColumns('stats_common', $aParams, $allFields);
            break;

        case 'stats_common' :
            if (isset($aParams['custom_columns']) && is_array($aParams['custom_columns'])) {
                $aColumns += $aParams['custom_columns'];
            } else {
                $aColumns += array('SUM(s.requests)' => 'sum_requests', 'SUM(s.impressions)' => 'sum_views', 'SUM(s.clicks)' => 'sum_clicks', 'SUM(s.conversions)' => 'sum_conversions');
            }

            if (isset($aParams['add_columns']) && is_array($aParams['add_columns'])) {
                $aColumns += $aParams['add_columns'];
            }
            break;

        case 'tracker' :
            $aColumns += array('t.clientid' => 'advertiser_id', 't.trackerid' => 'tracker_id', 't.trackername' => 'name');
            if ($allFields) $aColumns += array('t.description' => 'description', 't.viewwindow' => 'viewwindow', 't.clickwindow' => 'clickwindow', 't.blockwindow' => 'blockwindow', 't.variablemethod' => 'variablemethod', 't.appendcode' => 'appendcode');
            break;

        case 'variable' :
            $aColumns += array('v.variableid' => 'variable_id', 'v.trackerid' => 'tracker_id', 'v.name' => 'name', 'v.datatype' => 'type');
            //  variabletype field was removed in 0.2.0-alpha
#            if ($allFields) $aColumns += array('v.description' => 'description', 'v.variabletype' => 'variable_type');
            if ($allFields) $aColumns += array('v.description' => 'description', 'v.variablecode' => 'variablecode');
            break;

        case 'zone' :
            $aColumns += array('z.zoneid' => 'zone_id', 'z.affiliateid' => 'publisher_id', 'z.zonename' => 'name', 'z.delivery' => 'type');
            if ($allFields) $aColumns += array('z.description' => 'description', 'z.width' => 'width', 'z.height' => 'height', 'z.chain' => 'chain', 'z.prepend' => 'prepend', 'z.append' => 'append', 'z.appendtype' => 'appendtype', 'z.forceappend' => 'forceappend', 'z.inventory_forecast_type' => 'inventory_forecast_type', 'z.comments' => 'comments', 'z.cost' => 'cost', 'z.cost_type' => 'cost_type', 'z.cost_variable_id' => 'cost_variable_id', 'z.technology_cost' => 'technology_cost', 'z.technology_cost_type' => 'technology_cost_type', 'z.block' => 'block', 'z.capping' => 'capping', 'z.session_capping' => 'session_capping', 'z.category' => 'category', 'z.ad_selection' => 'ad_selection');
            break;

        case 'placement_zone_assoc' :
            $aColumns += array('pz.placement_zone_assoc_id' => 'placement_zone_assoc_id', 'pz.placement_id' => 'placement_id', 'pz.zone_id' => 'zone_id');
            break;
        }
        return $aColumns;
    }

    /**
     * Returns the fields required by a stats query for a given entity.
     *
     * @param string $entity
     * @return array
     */
    function _getStatsColumns($entity)
    {
        $aColumns = array('SUM(s.requests)' => 'sum_requests', 'SUM(s.impressions)' => 'sum_views', 'SUM(s.clicks)' => 'sum_clicks', 'SUM(s.conversions)' => 'sum_conversions');
        switch ($entity) {

        case 'ad' :         $aColumns += array('d.bannerid' => 'ad_id'); break;
        case 'advertiser' : $aColumns += array('a.clientid' => 'advertiser_id'); break;
        case 'agency' :     $aColumns += array('g.agencyid' => 'agency_id'); break;
        case 'placement' :  $aColumns += array('m.campaignid' => 'placement_id'); break;
        case 'publisher' :  $aColumns += array('p.affiliateid' => 'publisher_id'); break;
        case 'zone' :       $aColumns += array('z.zoneid' => 'zone_id'); break;
        }

        return $aColumns;
    }

    /**
     * Returns as array of table names and their aliases.
     *
     * @param string $entity
     * @param array $aParams
     * @param boolean $includeStats
     * @return array
     */
    function _getTables($entity, $aParams, $includeStats = false)
    {
        $conf = $GLOBALS['_MAX']['CONF'];

        $aTables = array();
        switch ($entity) {

        case 'ad' :
            $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_anonymous'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if ($includeStats) {
                if (!empty($aParams['publisher_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
                $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's');
            }
            break;

        case 'advertiser' :
            $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_anonymous'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['ad_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_width'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_height'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if ($includeStats) $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's', $conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            break;

        case 'ad_category_assoc' :
            $aTables += array($conf['table']['prefix'].$conf['table']['ad_category_assoc'] => 'ac');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['clients'] = 'a');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['placement_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_anonymous'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (isset($aParams['ad_width'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_height'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            break;

        case 'ad_zone_assoc' :
            $aTables += array($conf['table']['prefix'].$conf['table']['ad_zone_assoc'] => 'az');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z', $conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['affiliates'] => 'p');
            if (!empty($aParams['publisher_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['placement_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_anonymous'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['zone_inventory_forecast_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if (isset($aParams['ad_width'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_height'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            break;

        case 'agency' :
            $aTables += array($conf['table']['prefix'].$conf['table']['agency'] => 'g');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_anonymous'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['ad_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_width'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_height'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['publisher_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['affiliates'] => 'p');
            if (!empty($aParams['zone_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['affiliates'] => 'p', $conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if (!empty($aParams['zone_inventory_forecast_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['affiliates'] => 'p', $conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if ($includeStats) $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's', $conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['affiliates'] => 'p', $conf['table']['prefix'].$conf['table']['zones'] => 'z');
            break;

        case 'category' :
            $aTables += array($conf['table']['prefix'].$conf['table']['category'] => 'cat');
            break;

        case 'channel' :
            $aTables += array($conf['table']['prefix'].$conf['table']['channel'] => 'ch');
            break;

        case 'channel_limitation' :
            $aTables += array($conf['table']['prefix'].$conf['table']['acls_channel'] => 'chl');
            break;

        case 'image' :
            $aTables += array($conf['table']['prefix'].$conf['table']['images'] => 'i');
            break;

        case 'limitation' :
            $aTables += array($conf['table']['prefix'].$conf['table']['acls'] => 'l');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['clients'] = 'a');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            break;

        case 'placement' :
            $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a');
            if (!empty($aParams['ad_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_width'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (isset($aParams['ad_height'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['ad_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if ($includeStats) {
                if (!empty($aParams['publisher_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
                $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            }
            break;

        case 'placement_tracker' :
            $aTables += array($conf['table']['prefix'].$conf['table']['campaigns_trackers'] => 'mt');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['trackers'] => 't');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['trackers'] => 't');
            break;

        case 'placement_zone_assoc' :
            $aTables += array($conf['table']['prefix'].$conf['table']['placement_zone_assoc'] => 'pz');
            if (!empty($aParams['ad_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['affiliates'] => 'p');
            if (!empty($aParams['placement_active'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['placement_anonymous'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm');
            if (!empty($aParams['publisher_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if (!empty($aParams['zone_inventory_forecast_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            break;

        case 'publisher' :
            $aTables += array($conf['table']['prefix'].$conf['table']['affiliates'] => 'p');
            if (!empty($aParams['zone_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if (!empty($aParams['zone_inventory_forecast_type'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if ($includeStats) {
                $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's', $conf['table']['prefix'].$conf['table']['zones'] => 'z');
                if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            }
            break;

        case 'history_span' :
        case 'history_day' :
        case 'history_month' :
        case 'history_dow' :
        case 'history_hour' :
        case 'stats' :
            if (isset($aParams['custom_table'])) {
                $aTables += array($conf['table']['prefix'].$conf['table'][$aParams['custom_table']] => 's');
            } else {
                $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's');
            }

            if (!empty($aParams['agency_id'])) $aTables += array(
                $conf['table']['prefix'].$conf['table']['clients'] => 'a',
                $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['affiliates'] => 'p',
                $conf['table']['prefix'].$conf['table']['zones'] => 'z');

            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            if (!empty($aParams['publisher_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            break;

        case 'stats_by_entity' :
            if (isset($aParams['include']) && is_array($aParams['include'])) {
                // Fake needed parameters
                if (array_search('advertiser_id', $aParams['include']) !== false) $aParams['advertiser_id'] = 1;
                if (array_search('placement_id', $aParams['include']) !== false)  $aParams['placement_id'] = 1;
                if (array_search('publisher_id', $aParams['include']) !== false)  $aParams['publisher_id'] = 1;
            }
            $aTables += SqlBuilder::_getTables('stats', $aParams);
            break;

        case 'tracker' :
            $aTables += array($conf['table']['prefix'].$conf['table']['trackers'] => 't');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a');
            if (!empty($aParams['ad_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd', $conf['table']['prefix'].$conf['table']['campaigns'] => 'm', $conf['table']['prefix'].$conf['table']['campaigns_trackers'] => 'mt');
            if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['campaigns_trackers'] => 'mt');
            break;

        case 'variable' :
            $aTables += array($conf['table']['prefix'].$conf['table']['variables'] => 'v');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['clients'] => 'a', $conf['table']['prefix'].$conf['table']['trackers'] => 't');
            if (!empty($aParams['advertiser_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['trackers'] => 't');
            break;

        case 'zone' :
            $aTables += array($conf['table']['prefix'].$conf['table']['zones'] => 'z');
            if (!empty($aParams['agency_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['affiliates'] => 'p');
            if ($includeStats) $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's');
            if ($includeStats) {
                $aTables += array($conf['table']['prefix'].$conf['table']['data_summary_ad_hourly'] => 's');
                if (!empty($aParams['placement_id'])) $aTables += array($conf['table']['prefix'].$conf['table']['banners'] => 'd');
            }
            break;
        }
        return $aTables;
    }

    /**
     * Returns an table name => alias pair.
     *
     * The output of this method is identical the _getTables() above, providing
     * the second parameter, $aParams, is an empty array.
     *
     * @param string $entity
     * @return array    An array of the prefixed table name and table alias
     */
    function _getPrimaryTable($entity)
    {
        $conf = $GLOBALS['_MAX']['CONF'];

        $aTable = '';
        switch ($entity) {

        case 'ad' :                     $aTable = array($conf['table']['banners'] => 'd'); break;
        case 'advertiser' :             $aTable = array($conf['table']['clients'] => 'a'); break;
        case 'ad_category_assoc' :      $aTable = array($conf['table']['ad_category_assoc'] => 'ac'); break;
        case 'ad_zone_assoc' :          $aTable = array($conf['table']['ad_zone_assoc'] => 'az'); break;
        case 'agency' :                 $aTable = array($conf['table']['agency'] => 'g'); break;
        case 'category' :               $aTable = array($conf['table']['category'] => 'cat'); break;
        case 'image' :                  $aTable = array($conf['table']['images'] => 'i'); break;
        case 'limitation' :             $aTable = array($conf['table']['acls'] => 'l'); break;
        case 'placement' :              $aTable = array($conf['table']['campaigns'] => 'm'); break;
        case 'placement_tracker' :      $aTable = array($conf['table']['campaigns_trackers'] => 'mt'); break;
        case 'placement_zone_assoc' :   $aTable = array($conf['table']['placement_zone_assoc'] => 'pz'); break;
        case 'publisher' :              $aTable = array($conf['table']['affiliates'] => 'p'); break;
        case 'history_span' :
        case 'history_day' :
        case 'history_month' :
        case 'history_dow' :
        case 'history_hour' :
        case 'stats_by_entity' :
        case 'stats' :
                                        $aTable = array($conf['table']['data_summary_ad_hourly'] => 's');
                                        break;
        case 'tracker' :                $aTable = array($conf['table']['trackers'] => 't'); break;
        case 'variable' :               $aTable = array($conf['table']['variables'] => 'v'); break;
        case 'zone' :                   $aTable = array($conf['table']['zones'] => 'z'); break;
        }
        return $aTable;
    }

    /**
     * Returns an array of constraints given an entity with params.
     *
     * @param string $entity
     * @param array $aParams
     * @return array
     */
    function _getLimitations($entity, $aParams)
    {
        if (!empty($aParams) && is_array($aParams)) {
            $aParams = MAX_commonSlashArray($aParams);
        }

        $aLimitations = array();

        if (isset($aParams['ad_width'])) SqlBuilder::_addLimitation($aLimitations, 'ad_width', 'd.width', $aParams['ad_width']);
        if (isset($aParams['ad_height'])) SqlBuilder::_addLimitation($aLimitations, 'ad_height', 'd.height', $aParams['ad_height']);
        if (!empty($aParams['ad_type'])) SqlBuilder::_addLimitation($aLimitations, 'ad_type', 'd.storagetype', $aParams['ad_type']);
        if (!empty($aParams['ad_active'])) SqlBuilder::_addLimitation($aLimitations, 'ad_active', 'd.active', $aParams['ad_active']);
        if (!empty($aParams['placement_active'])) SqlBuilder::_addLimitation($aLimitations, 'placement_active', 'm.active', $aParams['placement_active']);
        if (!empty($aParams['placement_anonymous'])) SqlBuilder::_addLimitation($aLimitations, 'placement_anonymous', 'm.anonymous', $aParams['placement_anonymous']);
        if (!empty($aParams['zone_inventory_forecast_type'])) SqlBuilder::_addLimitation($aLimitations, 'zone_inventory_forecast_type', 'z.inventory_forecast_type', $aParams['zone_inventory_forecast_type'], MAX_LIMITATION_BITWISE);

        switch ($entity) {

        case 'ad' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'd.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'd.bannerid', $aParams['ad_id']);
            break;

        case 'advertiser' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'a.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'm.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'd.bannerid', $aParams['ad_id']);
            break;

        case 'ad_category_assoc' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'c.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'd.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'ac.ad_id', $aParams['ad_id']);
            if (!empty($aParams['ad_category_assoc_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_category_assoc_id', 'ac.ad_category_assoc_id', $aParams['ad_category_assoc_id']);
            break;

        case 'ad_zone_assoc' :
            if (!empty($aParams['agency_id'])) {
               SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'p.agencyid', $aParams['agency_id']);
               SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            }
            if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'z.affiliateid', $aParams['publisher_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 'az.zone_id', $aParams['zone_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'd.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'az.ad_id', $aParams['ad_id']);
            if (!empty($aParams['ad_zone_assoc_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_zone_assoc_id', 'az.ad_zone_assoc_id', $aParams['ad_zone_assoc_id']);
            break;

        case 'agency' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'g.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'a.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'm.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'd.bannerid', $aParams['ad_id']);
            if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'p.affiliateid', $aParams['publisher_id']);
            if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 'z.zoneid', $aParams['zone_id']);
            break;

        case 'category' :
            if (!empty($aParams['name'])) SqlBuilder::_addLimitation($aLimitations, 'name', 'cat.name', $aParams['name']);
            break;

        case 'channel' :
            if (isset($aParams['publisher_id'])) {
                SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'ch.affiliateid', $aParams['publisher_id']);
            } else if (isset($aParams['channel_type']) && $aParams['channel_type'] == 'publisher') {
                SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'ch.affiliateid', 0, MAX_LIMITATION_NOT_EQUAL);
            }
            if (!empty($aParams['channel_id'])) SqlBuilder::_addLimitation($aLimitations, 'channel_id', 'ch.channelid', $aParams['channel_id']);
            if (isset($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'agencyid', $aParams['agency_id']);
            break;

        case 'channel_limitation' :
            if (!empty($aParams['channel_id'])) SqlBuilder::_addLimitation($aLimitations, 'channel_id', 'chl.channelid', $aParams['channel_id']);
            break;

        case 'image' :
            if (!empty($aParams['file_name'])) SqlBuilder::_addLimitation($aLimitations, 'file_name', 'i.filename', $aParams['file_name']);
            break;

        case 'limitation' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'c.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'd.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'l.bannerid', $aParams['ad_id']);
            break;

        case 'placement' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'm.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'd.bannerid', $aParams['ad_id']);
            break;

        case 'placement_tracker' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'c.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) {
               SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
               SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 't.clientid', $aParams['advertiser_id']);
            }
            if (!empty($aParams['tracker_id'])) SqlBuilder::_addLimitation($aLimitations, 'tracker_id', 'mt.trackerid', $aParams['tracker_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'mt.campaignid', $aParams['placement_id']);
            break;

        case 'placement_zone_assoc' :
            if (!empty($aParams['agency_id'])) {
               SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'p.agencyid', $aParams['agency_id']);
               SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'c.agencyid', $aParams['agency_id']);
            }
            if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'z.affiliateid', $aParams['publisher_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 'pz.zone_id', $aParams['zone_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'pz.placement_id', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'd.bannerid', $aParams['ad_id']);
            if (!empty($aParams['placement_zone_assoc_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_zone_assoc_id', 'pz.placement_zone_assoc_id', $aParams['placement_zone_assoc_id']);
            break;

        case 'publisher' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'p.agencyid', $aParams['agency_id']);
            if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'p.affiliateid', $aParams['publisher_id']);
            if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 'z.zoneid', $aParams['zone_id']);
            break;

        case 'history_span' :
        case 'history_day' :
        case 'history_month' :
        case 'history_dow' :
        case 'history_hour' :
        case 'stats' :
            if (!empty($aParams['agency_id'])) {

                // join to affiliates if stats by zone
                if (!empty($aParams['publisher_id']) ||
                    !empty($aParams['zone_id']) ||
                    isset($aParams['zone_type']) ||
                    isset($aParams['zone_width']) ||
                    isset($aParams['zone_height'])) {
                        SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'p.agencyid', $aParams['agency_id']);
                }
                SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            }
            if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'z.affiliateid', $aParams['publisher_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 's.zone_id', $aParams['zone_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'd.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 's.ad_id', $aParams['ad_id']);
            if (!empty($aParams['custom_table']) && $aParams['custom_table'] == 'data_intermediate_ad_connection') {
                if (!empty($aParams['day_begin'])) $aLimitations[]="s.tracker_date_time>='{$aParams['day_begin']} 00:00:00'";
                if (!empty($aParams['day_end'])) $aLimitations[]="s.tracker_date_time<='{$aParams['day_end']} 23:59:59'";
            } else {
                if (!empty($aParams['day_begin'])) $aLimitations[]="s.day>='{$aParams['day_begin']}'";
                if (!empty($aParams['day_end'])) $aLimitations[]="s.day<='{$aParams['day_end']}'";
            }
            break;

        case 'stats_by_entity' :
            $aLimitations += SqlBuilder::_getLimitations('stats', $aParams);
            if (isset($aParams['zone_id']) && $aParams['zone_id'] == 0) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 's.zone_id', 0);
            break;

        case 'tracker' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 't.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['tracker_id'])) SqlBuilder::_addLimitation($aLimitations, 'tracker_id', 't.trackerid', $aParams['tracker_id']);
            if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'mt.campaignid', $aParams['placement_id']);
            if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 'd.bannerid', $aParams['ad_id']);
            break;

        case 'variable' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'a.agencyid', $aParams['agency_id']);
            if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 't.clientid', $aParams['advertiser_id']);
            if (!empty($aParams['tracker_id'])) SqlBuilder::_addLimitation($aLimitations, 'tracker_id', 'v.trackerid', $aParams['tracker_id']);
            if (!empty($aParams['variable_id'])) SqlBuilder::_addLimitation($aLimitations, 'variable_id', 'v.variableid', $aParams['variable_id']);
            break;

        case 'zone' :
            if (!empty($aParams['agency_id'])) SqlBuilder::_addLimitation($aLimitations, 'agency_id', 'p.agencyid', $aParams['agency_id']);
            if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'z.affiliateid', $aParams['publisher_id']);
            if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 'z.zoneid', $aParams['zone_id']);
            if (isset($aParams['zone_type'])) SqlBuilder::_addLimitation($aLimitations, 'zone_type', 'z.delivery', $aParams['zone_type']);
            if (isset($aParams['zone_width'])) SqlBuilder::_addLimitation($aLimitations, 'zone_width', 'z.width', $aParams['zone_width']);
            if (isset($aParams['zone_height'])) SqlBuilder::_addLimitation($aLimitations, 'zone_height', 'z.height', $aParams['zone_height']);
            break;
        }
        return $aLimitations;
    }

    /**
     * Builds the limitations array.
     *
     * @param array $aLimitations
     * @param string $entityIdName
     * @param string $columnName
     * @param string $value
     * @param string $comparison_type (optional)
     */
    function _addLimitation(&$aLimitations, $entityIdName, $columnName, $value, $comparison_type = MAX_LIMITATION_EQUAL)
    {
        // Add single quotes around non-integer columns
        if (($entityIdName == 'ad_type')
         || ($entityIdName == 'ad_active')
         || ($entityIdName == 'placement_active')
         || ($entityIdName == 'placement_anonymous')
        ) {
            $value = "'" . str_replace(',', "','", $value) . "'";
        }

        // If there are multiple values, use IN instead of =
        if (strpos($value, ',') !== false) {
            $aLimitations[] = "$columnName IN ($value)";
        } else {
            switch ($comparison_type) {
            case MAX_LIMITATION_NOT_EQUAL:
                $aLimitations[] = "$columnName != $value";
                break;
            case MAX_LIMITATION_BITWISE:
                $aLimitations[] = "($columnName & $value > 0)";
                break;
            default:
                $aLimitations[] = "$columnName = $value";
                break;
            }
        }
    }

    /**
     * Returns an array of constraints given an entity with params.
     *
     * @param string $entity
     * @param array $aParams
     * @return array
     */
    function _getStatsLimitations($entity, $aParams)
    {
        $aLimitations = array();

        if (!empty($aParams['agency_id'])) {
            switch ($entity) {

            case 'advertiser' :
            case 'placement' :
            case 'ad' :
                $aLimitations[] = "a.agencyid={$aParams['agency_id']}";
                break;
            case 'publisher' :
            case 'zone' :
                $aLimitations[] = "p.agencyid={$aParams['agency_id']}";
                break;
            default :
                $aLimitations[] = "p.agencyid={$aParams['agency_id']}";
                $aLimitations[] = "a.agencyid={$aParams['agency_id']}";
            }
        }

        if (!empty($aParams['publisher_id'])) SqlBuilder::_addLimitation($aLimitations, 'publisher_id', 'z.affiliateid', $aParams['publisher_id']);
        if (!empty($aParams['advertiser_id'])) SqlBuilder::_addLimitation($aLimitations, 'advertiser_id', 'm.clientid', $aParams['advertiser_id']);
        if (!empty($aParams['zone_id'])) SqlBuilder::_addLimitation($aLimitations, 'zone_id', 's.zone_id', $aParams['zone_id']);
        if (!empty($aParams['placement_id'])) SqlBuilder::_addLimitation($aLimitations, 'placement_id', 'd.campaignid', $aParams['placement_id']);
        if (!empty($aParams['ad_id'])) SqlBuilder::_addLimitation($aLimitations, 'ad_id', 's.ad_id', $aParams['ad_id']);
        if (!empty($aParams['day_begin'])) $aLimitations[]="s.day>='{$aParams['day_begin']}'";
        if (!empty($aParams['day_end'])) $aLimitations[]="s.day<='{$aParams['day_end']}'";

        return $aLimitations;
    }

    /**
     * Builds an array of contraints given a list of tables.
     *
     * @todo Added some code specific for data_summary_ad_arrival_hourly table
     *
     * @param array $aTables
     * @return array
     */
    function _getTableLimitations($aTables)
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $aTableLimitations = array();
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['agency']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['clients']])))
            $aTableLimitations[]='g.agencyid=a.agencyid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['agency']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['affiliates']])))
            $aTableLimitations[]='g.agencyid=p.agencyid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['affiliates']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['zones']])))
            $aTableLimitations[]='p.affiliateid=z.affiliateid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['clients']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['campaigns']])))
            $aTableLimitations[]='a.clientid=m.clientid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['clients']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['trackers']])))
            $aTableLimitations[]='a.clientid=t.clientid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['campaigns']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['banners']])))
            $aTableLimitations[]='m.campaignid=d.campaignid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['campaigns']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['placement_zone_assoc']])))
            $aTableLimitations[]='m.campaignid=pz.placement_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['campaigns']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['campaigns_trackers']])))
            $aTableLimitations[]='m.campaignid=mt.campaignid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['trackers']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['campaigns_trackers']])))
            $aTableLimitations[]='t.trackerid=mt.trackerid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['banners']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['data_summary_ad_hourly']])))
            $aTableLimitations[]='d.bannerid=s.ad_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['banners']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['data_intermediate_ad_connection']])))
            $aTableLimitations[]='d.bannerid=s.ad_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['banners']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['ad_category_assoc']])))
            $aTableLimitations[]='d.bannerid=ac.ad_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['banners']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['ad_zone_assoc']])))
            $aTableLimitations[]='d.bannerid=az.ad_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['banners']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['acls']])))
            $aTableLimitations[]='d.bannerid=l.bannerid';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['zones']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['data_summary_ad_hourly']])))
            $aTableLimitations[]='z.zoneid=s.zone_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['zones']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['data_intermediate_ad_connection']])))
            $aTableLimitations[]='z.zoneid=s.zone_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['zones']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['ad_zone_assoc']])))
            $aTableLimitations[]='z.zoneid=az.zone_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['zones']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['placement_zone_assoc']])))
            $aTableLimitations[]='z.zoneid=pz.zone_id';
        if (!empty($aTables[$conf['table']['prefix'].$conf['table']['channel']]) && (!empty($aTables[$conf['table']['prefix'].$conf['table']['affiliates']])))
            $aTableLimitations[]='p.affiliateid=ch.affiliateid';
        return $aTableLimitations;
    }

    /**
     * Returns the grouping given the particular entity.
     *
     * @param string $entity
     * @param array $aParams
     * @return array
     */
    function _getGroupColumns($entity, $aParams)
    {
        $aGroupColumns = array();
        switch ($entity) {

        case 'history_day' :   $aGroupColumns[] = 'day'; break;
        case 'history_month' : $aGroupColumns[] = 'month'; break;
        case 'history_dow' :   $aGroupColumns[] = 'dow'; break;
        case 'history_hour' :  $aGroupColumns[] = 'hour'; break;

        case 'stats_by_entity' :
            $aGroupColumns = array('ad_id', 'zone_id');
            /*
            if (isset($aParams['include']) && is_array($aParams['include'])) {
                if (array_search('advertiser_id', $aParams['include']) !== false) $aGroupColumns[] = 'advertiser_id';
                if (array_search('placement_id', $aParams['include']) !== false)  $aGroupColumns[] = 'placement_id';
                if (array_search('ad_id', $aParams['include']) !== false)         $aGroupColumns[] = 'ad_id';
                if (array_search('publisher_id', $aParams['include']) !== false)  $aGroupColumns[] = 'publisher_id';
                if (array_search('zone_id', $aParams['include']) !== false)       $aGroupColumns[] = 'zone_id';
            }
            */
            if (isset($aParams['include'])) $aGroupColumns = array_merge($aGroupColumns, $aParams['include']);
            if (isset($aParams['exclude'])) $aGroupColumns = array_diff($aGroupColumns, $aParams['exclude']);
            break;

        }


        return count($aGroupColumns) ? $aGroupColumns : null;
    }

    /**
     * Returns the tables which need a LEFT JOIN given the particular entity.
     *
     * @param string $entity
     * @param array $aParams
     * @return array
     */
    function _getLeftJoinedTables($entity, $aParams, $aTables = null)
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $aLeftJoinedTables = array();
        switch ($entity) {
        case 'stats_by_entity' :
            if (isset($aParams['exclude']) && !empty($aParams['agency_id'])) {
                if (array_search('zone_id', $aParams['exclude']) !== false) {
                    // include deleted zones and direct selection in advertiser stats
                    $aLeftJoinedTables[$conf['table']['prefix'].$conf['table']['zones']] = 'z';
                    $aLeftJoinedTables[$conf['table']['prefix'].$conf['table']['affiliates']] = 'p';
                }
            }
            break;
        case 'history_span' :
        case 'history_day' :
        case 'history_month' :
        case 'history_dow' :
        case 'history_hour' :
            // include deleted zones and direct selection in history stats
            $aLeftJoinedTables[$conf['table']['prefix'].$conf['table']['zones']] = 'z';
            $aLeftJoinedTables[$conf['table']['prefix'].$conf['table']['affiliates']] = 'p';
            break;
        }

        return count($aLeftJoinedTables) ? $aLeftJoinedTables : null;
    }

    // +---------------------------------------+
    // | SQL operations methods                |
    // |                                       |
    // | for entity builders                   |
    // +---------------------------------------+

    /**
     * A method to delete complete rows from the specified table(s), limited
     * to the rows specified by the $aLimitations.
     *
     * Note: Due to incompatibilities between MySQL <= 4.0 and MySQL >= 4.1
     * in the way multi-table deletes are performed, it is not possible to
     * use table aliases. As a result, all tables are referred to in raw
     * table name format, and the limitations (if they exist) needed to be
     * converted back from alias format to use raw table names. It's a bit
     * of a hack, but unless this DAL supports MySQL version detection, it's
     * all that can be done.
     *
     * @param array $aTables An array, containing the table names to delete from, and
     *                       the associated table SQL alias(es). For example:
     *                       array(
     *                           'ad_zone_assoc' => 'az'
     *                       )
     * @param array $aLimitations An array, containing a the required SQL limitations
     *                            to be used when performing the delete. For example:
     *                            array(
     *                                0 => 'az.zone_id=1',
     *                                1 => 'az.ad_id=1'
     *                            )
     * @param array $aOtherTables An array, containing any other tables that need
     *                            to be referenced by the limitations in order to
     *                            carry out the delete. These are passed in using the
     *                            same format as the $aTables array.
     * @return mixed Either an integer, representing the number of rows affected by
     *               the delete, or a PEAR::Error.
     */
    function _delete($aTables, $aLimitations, $aOtherTables = null)
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        // Set the tables to delete from, using the table aliases
        if (!is_array($aTables) || empty($aTables)) {
            PEAR::raiseError('Invalid parameter $aTables');

        }
        $deleteTables = '';
        foreach ($aTables as $table => $alias) {
            // Check the table prefix is in place
            if (!empty($conf['table']['prefix']) && (stristr($table, $conf['table']['prefix']) === false)) {
                $table = $conf['table']['prefix'] . $table;
            }
            $deleteTables .= ($deleteTables == '') ? $table : ', ' . $table;
        }
        // Set the list of table names and aliases to use
        $tables = '';
        foreach ($aTables as $table => $alias) {
            // Check the table prefix is in place
            if (!empty($conf['table']['prefix']) && (stristr($table, $conf['table']['prefix']) === false)) {
                $table = $conf['table']['prefix'] . $table;
            }
            $tables .= ($tables == '') ? $table : ', ' . $table;
        }
        if (is_array($aOtherTables)) {
            foreach ($aOtherTables as $otherTable => $alias) {
                // Ensure we don't use table name twice
                if (is_null($aTables[$otherTable])) {
                    // Check the table prefix is in place
                    if (!empty($conf['table']['prefix']) && (stristr($otherTable, $conf['table']['prefix']) === false)) {
                        $otherTable = $conf['table']['prefix'] . $otherTable;
                    }
                    $tables .= ($tables == '') ? $otherTable : ', ' . $otherTable;
                }
            }
        }
        // Prepare a reverse lookup array of aliases to table names
        $aLimitationTables = array();
        foreach ($aTables as $table => $alias) {
            if (!empty($conf['table']['prefix']) && (stristr($table, $conf['table']['prefix']) === false)) {
                $table = $conf['table']['prefix'] . $table;
            }
            $aLimitationTables[$alias] = $table;
        }
        if (is_array($aOtherTables)) {
            foreach ($aOtherTables as $otherTable => $alias) {
                if (!empty($conf['table']['prefix']) && (stristr($otherTable, $conf['table']['prefix']) === false)) {
                    $otherTable = $conf['table']['prefix'] . $otherTable;
                }
                $aLimitationTables[$alias] = $otherTable;
            }
        }
        // Set the WHERE limitations
        $where = '';
        if (is_array($aLimitations)) {
            foreach ($aLimitations as $limitation) {
                // Convert the limitation from alias form to table name form
                foreach ($aLimitationTables as $alias => $table) {
                    $limitation = preg_replace("/^$alias\./", "$table.", $limitation);
                }
                $where .= ($where == '') ? ' WHERE ' . $limitation : ' AND ' . $limitation;
            }
        }
        // Set the final DELETE query
        $query = 'DELETE FROM ' . $deleteTables . ' USING ' . $tables . $where;
        // Doublecheck that there is something in the WHERE clause
        //  - to ensure that a bug does not delete the entire contents of a table!
        if (strlen($where) > 0) {
            $dbh = &OA_DB::singleton();
            return $dbh->exec($query);
        } else {
            return PEAR::raiseError('Invalid WHERE clause');
        }
    }


    /**
     * Performs an SQL insert.
     *
     * @param array $table Table name.
     * @param array $aVariables Map of column to value for the created row.
     * @return integer The newly inserted row id if the table has any
     * autoincrement field defined. Otherwise, true on success and false
     * on failure.
     */
    function _insert($table, $aVariables)
    {
        $do = OA_Dal::factoryDO($table);
        if ($do === false) {
            return false;
        }
        $success = $do->setFrom($aVariables);
        if (!$success === true) {
            return false;
        }
        return $do->insert();
    }


    function _getFieldIdName($table)
    {
        $dbh = &OA_DB::singleton();
        $schema = MDB2_Schema::factory($dbh);
        $definition = $schema->getDefinitionFromDatabase(array($table));
        foreach ($definition['tables'][$table]['fields'] as $fieldname => $dataField) {
            if (isset($dataField['autoincrement']) && 1 == $dataField['autoincrement']) {
                return $fieldname;
            }
        }
        return null;
    }

    /**
     * Wraps an SQL query.
     *
     * @param array $aColumns
     * @param array $aTables
     * @param array $aLimitations
     * @param array $aGroupColumns
     * @param string $primaryKey
     * @param array $aLeftJoinedTables
     * @return integer  The number of rows affected by the query
     */
    function _select($aColumns, $aTables, $aLimitations, $aGroupColumns, $primaryKey, $aLeftJoinedTables = null)
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $columns = '';
        if (is_array($aColumns)) {
            foreach ($aColumns as $column => $alias) {
                $columns .= ($columns == '') ? "SELECT $column AS $alias" : ",$column AS $alias";
            }
        } else {
            $columns = "SELECT $aColumns";
        }

        $prev_aliases = array();
        $tables = '';
        if (is_array($aTables)) {
            $x = 0;
            if (!is_null($aLeftJoinedTables)) {
                $aTables = array_diff($aTables, $aLeftJoinedTables);
            }
            $joinType = 'INNER';
            while (count($aTables) && $x < 50) {
                $x++;
                foreach ($aTables as $tableKey => $alias) {
                    $table = $tableKey;

                    //  check for prefix
                    if (!empty($conf['table']['prefix']) && stristr($table, $conf['table']['prefix']) === false) {
                        $table = $conf['table']['prefix'] . $table;
                    }

                    $joinLimitation = '';

                    if (count($prev_aliases)) {
                        if (is_array($aLimitations)) {
                            foreach ($aLimitations as $limitationKey => $limitation) {
                                if (preg_match("/({$alias}\.[a-z0-9_]+ *= *(".join('|', $prev_aliases).")\..+|(".join('|', $prev_aliases).")\.[a-z0-9_]+ *= *{$alias}\..+)/", $limitation)) {
                                    $joinLimitation = $limitation;
                                    unset($aLimitations[$limitationKey]);
                                    break;
                                }
                            }
                        }
                    } else {
                        $tables .= " FROM $table AS $alias";
                    }

                    if ($joinLimitation) {
                        $tables .= " $joinType JOIN $table AS $alias ON ($joinLimitation)";
                    } elseif (count($prev_aliases)) {
                        continue;
                    }

                    $prev_aliases[] = $alias;
                    unset ($aTables[$tableKey]);
                }

                if (!is_null($aLeftJoinedTables) && !count($aTables)) {
                    $aTables = $aLeftJoinedTables;
                    $aLeftJoinedTables = null;
                    $joinType = 'LEFT';
                }
            }
        } else {
            $tables = "FROM $aTables";
        }

        $where = '';
        if (is_array($aLimitations)) {
            foreach ($aLimitations as $limitation) {
                $where .= ($where == '') ? " WHERE $limitation" : " AND $limitation";
            }
        } else {
            $where = " WHERE $aLimitations";
        }

        $group = '';
        if (is_array($aGroupColumns) && count($aGroupColumns) > 0) {
            $group = ' GROUP BY ' . implode(',', $aGroupColumns);
        }

        $query = $columns . $tables . $where . $group;

        return SqlBuilder::_query($query, $primaryKey);
    }

    /**
     * Performs and SQL query.
     *
     * @param string $query
     * @param string $primaryKey
     * @return array    An array of entity records
     */
    function _query($query, $primaryKey)
    {
        $rs = DBC::NewRecordSet($query);
        $aDataEntities = array();
        if ($rs->find()) {
            while ($rs->fetch()) {
                $dataEntity = $rs->toArray();
                $aDataEntities[$dataEntity[$primaryKey]] = $dataEntity;
            }
            return $aDataEntities;
        } else {
            return false;
        }
    }


    /**
     * Performs an SQL update.
     *
     * @param array $aTable
     * @param array $aVariables
     * @param array $aLimitations
     * @return integer  The number of rows affected by the update
     */
    function _update($aTable, $aVariables, $aLimitations)
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $table = '';
        if (is_array($aTable)) {
            foreach ($aTable as $tableName => $alias) {
                $table = "$tableName AS $alias";
            }
        }

        $set = '';
        foreach ($aVariables as $name => $value) {
            if (is_string($value)) {
                $value = addslashes($value);
            }
            $set .= ($set == '') ? " SET $name='$value'" : ",$name='$value'";
        }

        $where = '';
        if (is_array($aLimitations)) {
            foreach ($aLimitations as $limitation) {
                $where .= ($where == '') ? " WHERE $limitation" : " AND $limitation";
            }
        }

        $query = "UPDATE $table" . $set . $where;
        $queryValid = true;

        $dbh =& OA_DB::singleton();
        return $dbh->exec($query);
    }
}
?>
