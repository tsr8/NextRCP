var $ = require("jquery");
require("jquery-migrate");
// var moment = require("moment");
require("jqueryui");
//require("jqueryui/jquery-ui.css");
require("daterangepicker");
import Tabulator from 'tabulator-tables';
require('tabulator-tables/dist/css/tabulator.css');
require('daterangepicker/daterangepicker.css');
import 'select2/dist/js/select2.full.js'
require('select2/dist/css/select2.css');
require('../../css/style.css');

var dtf = require("./dateformat.js");

(function() {
  $.ajaxSetup({
    headers: { 'RequestToken': OC.requestToken }
  });
    $( function() {

      var group1 = "project";
      var group2 = "user";
      var group3 = "day";
      var filterProjectId = "";
      var filterClientId = "";

        $(document).ready(function() {
            $("#dialog-confirm").dialog({
              autoOpen: false,
              modal: true
            });

          var start = moment().subtract(29, 'days');
          var end = moment();
          function cb(start, end) {
            $('#report-range span').html(start.format(dtf.dformat()) + ' - ' + end.format(dtf.dformat()));
          }
          $("#report-range").daterangepicker({
            timePicker: false,
            startDate: start,
            endDate: end,
            ranges: {
              'Today': [moment(), moment()],
              'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days': [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'Last 90 Days': [moment().subtract(89, 'days'), moment()],
              'Last 365 Days': [moment().subtract(364, 'days'), moment()],
              'This Month': [moment().startOf('month'), moment().endOf('month')],
              'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
              'The Month Before Last': [moment().subtract(2, 'month').startOf('month'), moment().subtract(2, 'month').endOf('month')],
              'This Year': [moment().startOf('year'), moment().endOf('year')],
              'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],

            },
            locale: {
                format: dtf.dformat(),
                firstDay: firstDay
              }
            },cb);
            $("#report-range").on('apply.daterangepicker', function(ev, picker) {
              start = picker.startDate;
              end = picker.endDate;
              getReport();
            });
          cb(start, end);
          $("#group1").select2();
          $("#group2").select2();
          $("#group3").select2();
          $('#group1').on("select2:select select2:unselect", function(e) {
            group1 = e.params.data.id;
            getReport();
          });
          $('#group2').on("select2:select select2:unselect", function(e) {
            group2 = e.params.data.id;
            getReport();
          });
          $('#group3').on("select2:select select2:unselect", function(e) {
            group3 = e.params.data.id;
            getReport();
          });
          getReport();
          $("#filter-project").select2({
            width: '200px',
            escapeMarkup : function(markup) { return markup; },
            placeholder: "Select project",
            allowClear: true,
            templateResult: function formatState (project) {
              var color = '#ffffff';
              if (project.color) {
                color = project.color;
              }
              var $state = $(
                '<span class="select-project"><span class="select-project-color" style="background-color:'+color+';" ></span>' + project.text + '</span>'
              );
              return $state;
            },
            ajax: {
              tags: true,
                url:  OC.generateUrl('/apps/nextrcp/ajax/projects'),

                dataType: 'json',
                delay: 250,

                processResults: function (data, page) { //json parse
                    return {
                        results: $.map(data.Projects,function(val, i){
                        return { id: val.id, text:val.name, color: val.color};
                        }),
                        pagination: {
                        more: false,
                        }
                    };
                },
                cache: false,

            },
        });

        $('#filter-project').on("select2:select select2:unselect", function(e) {


          filterProjectId = ($(e.target).val() != null)? $(e.target).val() : "";
          getReport();
        });



        $("#filter-client").select2({
          tags: true,
          width: '200px',
          escapeMarkup : function(markup) { return markup; },
          placeholder: "Select client",
          allowClear: true,
          ajax: {
            tags: true,
              url:  OC.generateUrl('/apps/nextrcp/ajax/clients'),

              dataType: 'json',
              delay: 250,
              processResults: function (data, page) { //json parse
                  return {
                      results: $.map(data.Clients,function(val, i){
                        return { id: val.id, text:val.name};
                      }),
                      pagination: {
                      more: false,
                      }
                  };
              },
              cache: false,

          },
      });


      $('#filter-client').on("select2:select select2:unselect", function(e) {


        filterClientId = ($(e.target).val() != null)? $(e.target).val() : "";
        getReport();
      });


        $('input.select2-input').attr('autocomplete', "xxxxxxxxxxx");



          function getReport(){
              var baseUrl = OC.generateUrl('/apps/nextrcp/ajax/report?name=&from='+start.unix()+'&to='+end.unix()+'&group1='+group1+'&group2='+group2+'&timegroup='+group3+'&filterProjectId='+filterProjectId+'&filterClientId='+filterClientId);
              function pad(n, width, z) {
                z = z || '0';
                n = n + '';
                return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
              }
              var endedAccessor = function(value, data, type, params, column){
                if (group1 != '' || group2 != '' || group3 != ''){
                  return '*';
                }
		return moment(data.time, dtf.dtformat()).add(moment.duration(data.totalDuration)).format(dtf.dtformat());
              }
              var totalDurationAccessor = function(value, data, type, params, column){
                var s = Math.floor( (data.totalDuration) % 60 );
                var m = Math.floor( (data.totalDuration/60) % 60 );
                var h = Math.floor( (data.totalDuration/(60*60)));
                return pad(h,2) + ':' + pad(m,2) + ':' + pad(s,2);
              }
              var whenAccessor = function(value, data, type, params, column){
                switch(group3) {
                case 'day':
                  return moment.unix(value).format(dtf.dformat());
                case 'month':
                  return moment.unix(value).format(dtf.mformat());
                case 'week':
                  return moment.unix(value).format('YYYY[W]W');
                case 'year':
                  return moment.unix(value).format('YYYY');
                default:
                  return moment.unix(value).format(dtf.dtformat());
                }
              }
              var nullCheckAccessor = function(value, data, type, params, column){
                return value ? value : '';
              }

              var money = function(value, data, type, params, component) {
                return value / 100;
              }
              var table = new Tabulator("#report", {
                ajaxURL:baseUrl,
                layout:"fitColumns",
                columns:[
                  //{title:"Id", field:"id", width:100}, //column has a fixed width of 100px;
                  {title:"#", field:"id", formatter:"rownum"},
                  {title:"Name", field:"name", widthGrow:1}, //column will be allocated 1/5 of the remaining space
                  {title:"Details", field:"details", widthGrow:1}, //column will be allocated 1/5 of the remaining space
                  {title:"User", field:"userUid", widthGrow:1}, //column will be allocated 1/5 of the remaining space
                  {title:"Project", field:"project",accessorDownload:nullCheckAccessor, widthGrow:1}, //column will be allocated 1/5 of the remaining space
                  {title:"Client", field:"client",accessorDownload:nullCheckAccessor, widthGrow:1}, //column will be allocated 1/5 of the remaining space
                  {title:"When", field:"time", widthGrow:1,accessorDownload:whenAccessor,formatter:function(cell, formatterParams, onRendered){
                    var t = cell.getValue();
                    switch(group3) {
                    case 'day':
                      return moment.unix(t).format(dtf.dformat());
                    case 'month':
                      return moment.unix(t).format(dtf.mformat());
                    case 'week':
                      return moment.unix(t).format('YYYY[W]W');
                    case 'year':
                      return moment.unix(t).format('YYYY');
                    default:
                      return moment.unix(t).format(dtf.dtformat());
                    }
                  }},
                  {title:"Cost", field:"cost", mutator:money, accessorDownload: nullCheckAccessor, widthGrow:1, bottomCalc: "sum", formatter: "money", bottomCalcFormatter: "money"}, //column will be allocated 1/5 of the remaining space
                  {title:"Total Duration", field:"totalDuration",accessorDownload:totalDurationAccessor,formatter:function(cell, formatterParams, onRendered){
                    //cell - the cell component
                    //formatterParams - parameters set for the column
                    //onRendered - function to call when the formatter has been rendered
                    var duration = cell.getValue();
                    var s = Math.floor( (duration) % 60 );
                    var m = Math.floor( (duration/60) % 60 );
                    var h = Math.floor( (duration/(60*60)));
                    return pad(h,2) + ':' + pad(m,2) + ':' + pad(s,2);

                  },bottomCalc:"sum", bottomCalcParams:{
    			          precision:1,
		                },bottomCalcFormatter:function(cell, formatterParams, onRendered){
                      //cell - the cell component
                      //formatterParams - parameters set for the column
                      //onRendered - function to call when the formatter has been rendered
                      var duration = cell.getValue();
                      var s = Math.floor( (duration) % 60 );
                      var m = Math.floor( (duration/60) % 60 );
                      var h = Math.floor( (duration/(60*60)));

                      return pad(h,2) + ':' + pad(m,2) + ':' + pad(s,2);

                    }}, //column will be allocated 1/5 of the remaining space
                    {title:"Ended", field:"ended",visible:false,accessorDownload:endedAccessor,formatter:function(cell, formatterParams, onRendered){
                      //cell - the cell component
                      //formatterParams - parameters set for the column
                      //onRendered - function to call when the formatter has been rendered
                      if (group1 != '' || group2 != '' || group3 != ''){
                        return '*';
                      }
                      var time = cell.getRow().getData().time;
                      var duration = cell.getRow().getData().totalDuration;
                      return moment.unix(parseInt(time) + parseInt(duration)).format(dtf.dtformat());

                    }},
              ],
                ajaxResponse:function(url, params, response){

                  return response.items; //return the tableData property of a response json object
              },
              });
              $("#download-csv").off().click(function(){
                table.showColumn("ended");
                table.download("csv", "data.csv");
                table.hideColumn("ended");
                return false;
            });
            $("#download-json").off().click(function(){
              table.showColumn("ended");
              table.download("json", "data.json");
              table.hideColumn("ended");
              return false;
            });
          }
      });


      } );
}());
