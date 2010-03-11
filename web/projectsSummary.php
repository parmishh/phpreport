<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */

    /* We check authentication and authorization */
    require_once('phpreport/web/auth.php');

    /* Include the generic header and sidebar*/
    define(PAGE_TITLE, "PhpReport - Projects Summary");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once('phpreport/web/services/WebServicesFunctions.php');

?>

<script type="text/javascript">

    Ext.onReady(function(){

        Ext.ux.DynamicGridPanel = Ext.extend(Ext.grid.GridPanel, {

          initComponent: function(){
            /**
             * Default configuration options.
             *
             * You are free to change the values or add/remove options.
             * The important point is to define a data store with JsonReader
             * without configuration and columns with empty array. We are going
             * to setup our reader with the metaData information returned by the server.
             * See http://extjs.com/deploy/dev/docs/?class=Ext.data.JsonReader for more
             * information how to configure your JsonReader with metaData.
             *
             * A data store with remoteSort = true displays strange behaviours such as
             * not to display arrows when you sort the data and inconsistent ASC, DESC option.
             * Any suggestions are welcome
             */
            var config = {
              stateful: true,
              stateId: 'projectCustomerGrid',
              loadMask: true,
              stripeRows: true,
              ds: new Ext.data.Store({
                    url: this.storeUrl,
                    reader: new Ext.data.JsonReader()
              }),
              columns: []
            };

            Ext.apply(this, config);
            Ext.apply(this.initialConfig, config);

            Ext.ux.DynamicGridPanel.superclass.initComponent.apply(this, arguments);
          },

          onRender: function(ct, position){
            this.colModel.defaultSortable = true;

            Ext.ux.DynamicGridPanel.superclass.onRender.call(this, ct, position);

          }
        });

        var customersGrid = new Ext.ux.DynamicGridPanel({
            id: 'CustomersGrid',
            storeUrl: 'services/getProjectCustomerReportJsonService.php?<?php

                if ($sid!="")
                    echo "&sid=" . $sid;?>',
            rowNumberer: false,
            checkboxSelModel: false,
            loadMask: true,
            columnLines: true,
            frame: true,
            title: 'Project - Customer Worked Hours Report',
            iconCls: 'silk-table',
        });


            customersGrid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(customersGrid.store.reader.jsonData.columns) === 'object') {
                var columns = [];

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(customersGrid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(customersGrid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(customersGrid.store.reader.jsonData.columns, function(column){
                  columns.push(column);
                });

              /**
               * Setting column model configuration
               */
                customersGrid.getColumnModel().setConfig(columns);

                // We add 33 pixels to the width because it doesn't count
                // the vertical scroll bar
                customersGrid.setSize(customersGrid.getColumnModel().getTotalWidth() + 33, 500);

                summaryTabs.setSize(customersGrid.getColumnModel().getTotalWidth() + 33, 500);

                if (!summaryTabs.rendered)
                    summaryTabs.render(Ext.get("content"));

              }

            }, this);

        var usersGrid = new Ext.ux.DynamicGridPanel({
            id: 'UsersGrid',
            storeUrl: 'services/getProjectUserReportJsonService.php?<?php

                if ($sid!="")
                    echo "&sid=" . $sid;?>',
            rowNumberer: false,
            checkboxSelModel: false,
            loadMask: true,
            columnLines: true,
            frame: true,
            title: 'Project - User Worked Hours Report',
            iconCls: 'silk-table',
        });


            usersGrid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(usersGrid.store.reader.jsonData.columns) === 'object') {
                var columns = [];

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(usersGrid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(usersGrid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(usersGrid.store.reader.jsonData.columns, function(column){
                  columns.push(column);
                });

              /**
               * Setting column model configuration
               */
                usersGrid.getColumnModel().setConfig(columns);

                // We add 33 pixels to the width because it doesn't count
                // the vertical scroll bar
                usersGrid.setSize(usersGrid.getColumnModel().getTotalWidth() + 33, 500);

              }

            }, this);


        var summaryTabs = new Ext.TabPanel({
            activeTab: 0,
            frame: true,
            plain: true,
            items:[
                customersGrid,
                usersGrid
            ],
            listeners: { 'tabchange' : function(tabPanel, tab){
                    tabPanel.setSize(tab.getColumnModel().getTotalWidth() + 33, 500);
                }
            }
        });

        customersGrid.store.load();
        usersGrid.store.load();

    })

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
