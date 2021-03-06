@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-lg-12">
            <div class="row page-header">
                <h2 class="col-lg-4">Tournaments</h2>
            </div>

            {!! Form::model($tournament, array("route" => array("admin.tournaments.update", $tournament->id), "method" => "put")) !!}
            <fieldset>
                <legend>Tournament Details</legend>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('name', 'Name') !!}
                        {!! Form::text('name', null, array('class' => 'form-control')) !!}
                    </div>
                    {{--<div class="form-group">--}}
                        {{--{!! Form::label('tournament_sport_id', 'Sports') !!}<br/>--}}
                        {{--{!! Form::select('tournament_sport_id', $sports, null, array("" => "", "class"=>"sport-multiselect form-control")) !!}--}}
                    {{--</div>--}}

                    {{--<div class="form-group">--}}
                        {{--{!! Form::label('competition_id', 'Competitions') !!}<br/>--}}
                        {{--{!! Form::select('competition_id', $competitions, null, array("" => "","class"=>"competition-multiselect form-control")) !!}--}}
                    {{--</div>--}}

                    <div class="form-group event-group-container" id="event-group">
                        {!! Form::label('type', 'Select Type') !!}<br/>
                        {!! Form::select('type', [0 => 'Race', 1 => 'Sport'], null, array("id"=>"type", "class" => "form-control", 'placeholder' => '--Select a type--')) !!}
                        <a style='display:none;' href="#" class="event-group-toggle" data-target="#future-meeting">Select
                            Future Meeting</a>
                    </div>

                    <div class="form-group">
                        {!! Form::label('event_group_id', 'Event Group') !!}<br/>
                        {!! Form::select('event_group_id', $eventGroups, null, array("" => "","class" => "event-multiselect form-control")) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('tournament_buyin_id', "Ticket Value") !!}
                        {!! Form::select('tournament_buyin_id', $buyins, null, array("class" => "form-control")) !!}
                        <label id="limit_applies" style="display:none;">
                            {!! Form::hidden('free_tournament_buyin_limit_flag', false) !!}
                            {!! Form::checkbox('free_tournament_buyin_limit_flag', true, null) !!} Free Buyin Limit Applies
                        </label>
                    </div>

                    <div class="form-group">
                        {!! Form::label('jackpot', "Jackpot") !!}

                        <label class="radio-inline">
                            {!! Form::radio('jackpot_flag', 0, true) !!} No
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('jackpot_flag', 1) !!} Yes
                        </label>
                    </div>

                    <div class="form-group" id="parent-tournament" @if( ! $tournament->jackpot_flag ) style="display:none;" @endif>
                        {!! Form::label('parent_tournament_id', 'Parent Tournament') !!}
                        {!! Form::select('parent_tournament_id', $parentTournaments, null, array('class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('start_currency', 'Starting Currency ') !!}
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            {!! Form::number('start_currency', $tournament->start_currency/100, array("class" => "form-control")) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('email_flag', "Send Email: ") !!}
                        {!! Form::select('email_flag', array('No', 'Yes'), 1, array("class" => "form-control")) !!}
                    </div>

                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('description', "Description") !!}
                        {!! Form::textarea('description', null, array('class' => 'form-control')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('tod_flag', 'Tournament of the Day') !!}
                        {!! Form::select('tod_flag', $tod, null, array("class" => "form-control")) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('Status', "Publish status") !!}

                        <label class="radio-inline">
                            {!! Form::radio('status_flag', 1, true) !!} Yes
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('status_flag', 0) !!} No
                        </label>
                    </div>

                    <div class="form-group">
                        {!! Form::label('minimum_prize_pool', 'Minimum Prize Pool ') !!}
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            {!! Form::number('minimum_prize_pool', $tournament->minimum_prize_pool/100, array("class" => "form-control")) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label("tournament_labels", "Tournament Labels") !!}
                        {!! Form::select('tournament_labels[]', $labels, $tournament->tournamentlabels ? $tournament->tournamentlabels->lists('id') : null, array("class"=>"form-control", "multiple"=>"multiple")) !!}
                    </div>

                    {{--<div class="form-group">--}}
                        {{--{!! Form::label('tournament_groups', "Tournament Groups") !!}<br/>--}}
                        {{--{!! Form::select('tournament_groups[]', $groups, $tournament->groups ? $tournament->groups->lists('id')->all() : null, array('class' => 'form-control group-multiselect', 'multiple' => 'multiple')) !!}--}}
                    {{--</div>--}}

                    <div class="form-group">
                        {!! Form::label('free_credit_flag', "Free credit prize ") !!}

                        <label class="radio-inline">
                            {!! Form::radio('free_credit_flag', 0, true) !!} No
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('free_credit_flag', 1) !!} Yes
                        </label>
                    </div>

                    <div class="form-group">
                        {!! Form::label('tournament_prize_format', 'Prize Payout Format') !!}
                        {!! Form::select('tournament_prize_format', $prizeFormats, $tournament->tournament_prize_format ?: 3, array('class'=>'form-control')) !!}
                    </div>
                </div>
            </fieldset>

            <fieldset id="markets" style="display:none">
                <legend>Markets</legend>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Market</th>
                        <th>Line</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody id="market-table-body">
                    </tbody>
                </table>
            </fieldset>

            <fieldset>
                <legend>Advanced Settings</legend>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>
                            {!! Form::checkbox('closed_betting_on_first_match_flag', 1) !!} Close betting on first event
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            {!! Form::checkbox('reinvest_winnings_flag', 1, true) !!} Allow reinvestment of winnings
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            {!! Form::checkbox('bet_limit_flag', 1) !!} Implement Bet Limit
                        </label>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label("bet_limit_per_event", "Bet limit per event ") !!}
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            {!! Form::number('bet_limit_per_event', $tournament->bet_limit_per_event/100, array("class" => "form-control")) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('entries_close', 'Entries Close After') !!}
                        <div class="form-group">
                            <div class="col-lg-6">
                                {!! Form::select('entries_close_after', array("Select Event"), null, array("class"=>"form-control events-selector")) !!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::datetime('entries_close', $tournament->entries_close, array("class"=>"event-date")) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Rebuys</legend>

                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('rebuys', 'No. Rebuys') !!}
                        {!! Form::number('rebuys', null, array('class'=>'form-control')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('tournament_rebuy_buyin_id', "Buyin Value") !!}
                        {!! Form::select('tournament_rebuy_buyin_id', $buyins, null, array("class" => "form-control")) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('rebuy_currency', 'Rebuy Currency ') !!}
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            {!! Form::number('rebuy_currency', $tournament->rebuy_currency ? $tournament->rebuy_currency/100 : 1000, array("class" => "form-control")) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('rebuy_end_after', "Rebuys end after") !!}
                        <div class="form-group">
                            <div class="col-lg-6">
                                {!! Form::select('rebuy_end_after', array("Select Event"), null, array("class"=>"form-control events-selector")) !!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::datetime('rebuy_end', $tournament->rebuy_end, array("class"=>"event-date")) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('topups', 'No. Topups') !!}
                        {!! Form::number('topups', null, array("class" =>"form-control")) !!}
                    </div>

                    <div class="form-group">
                        <div class="form-group">
                            {!! Form::label('tournament_topup_buyin_id', "Top Up Value") !!}
                            {!! Form::select('tournament_topup_buyin_id', $buyins, null, array("class" => "form-control")) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('topup_currency', 'Topup Currency ') !!}
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            {!! Form::number('topup_currency', $tournament->topup_currency ? $tournament->topup_currency/100 : 1000, array("class" => "form-control")) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('topup_end_after', "Topups start after") !!}
                        <div class="form-group">
                            <div class="col-lg-6">
                                {!! Form::select('topup_start_after', array("Select Event"), null, array("class"=>"form-control events-selector")) !!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::datetime('topup_start_date', $tournament->topup_start_date, array("class"=>"event-date")) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('topup_end_after', "Topups end after") !!}
                        <div class="form-group">
                            <div class="col-lg-6">
                                {!! Form::select('topup_end_after', array("Select Event"), null, array("class"=>"form-control events-selector")) !!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::datetime('topup_end_date', $tournament->topup_end_date, array("class" => "event-date")) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Tournament Sponsors</legend>

                <div class="form-group">
                    {!! Form::label('tournament_sponsor_name', 'Tournament Sponsor Name') !!}
                    {!! Form::text('tournament_sponsor_name', null, array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('tournament_sponsor_logo', 'Tournament Sponsor Logo') !!}
                    {!! Form::text('tournament_sponsor_logo', null, array('class' => 'form-control')) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('tournament_sponsor_logo_link', 'Tournament Sponsor Logo Link') !!}
                    {!! Form::text('tournament_sponsor_logo_link', null, array('class' => 'form-control')) !!}
                </div>
            </fieldset>

            <div class="form-group">
                <div class="col-md-6">
                    {!! Form::submit('Save', array('class' => 'btn btn-primary form-control')) !!}
                </div>
                <div class="col-md-6">
                    {!! link_to_route('admin.tournaments.index', "Cancel", array(), array('class'=>'btn btn-danger form-control')) !!}
                </div>
            </div>

        </div>
    </div>

    <script type="text/javascript">

        var events = [];
        $('.datepicker').datetimepicker({'format' : 'YYYY-MM-DD HH:mm'});
        function createSelectOptions(json) {
            var html = $();

            $.each(json, function(index, value){
                html = html.add($('<option></option>').text(value.name).val(value.id));
            });

            return html;
        }

        $('#type').change(function () {
            if (!$(this).val()) {
                return;
            }

            var val = $(this).val();

            if (!$.isArray(val)) {
                val = [val];
            }

            $.get('/admin/tournaments/get-event-groups-by-type/' + val)
                    .done(function (data) {
                        $('#event_group_id').html(createSelectOptions(data));
//                        $('#event_group_id').change();
                    });

        });

        $('#tournament_sport_id').change(function(){
            if ( ! $(this).val()) {return;}

            var val = $(this).val();

            if ( ! $.isArray(val) ) {
                val = [val];
            }

            $.each(val, function (index, value) {
                $.get('/admin/tournaments/get-competitions/' + value)
                        .done(function(data) {
                            $('#competition_id').html(createSelectOptions(data));
                            $('#competition_id').change();
                            $('#event_group_id').change();
                            $('.sport-multiselect').multiselect("rebuild");
                            $('.event-multiselect').multiselect("rebuild");
                            $('.competition-multiselect').multiselect("rebuild");
                        });
            });


        });

        $('#competition_id').change(function(){
            if ( ! $(this).val()) {return;}

            var val = $(this).val();

            if ( ! $.isArray(val) ) {
                val = [val];
            }

            $.each(val, function (index, value) {
                $.get('/admin/tournaments/get-event-groups/' + value)
                        .done(function(data){
                            $('#event_group_id').html(createSelectOptions(data));
                            $('#event_group_id').change();
                            $('.sport-multiselect').multiselect("rebuild");
                            $('.event-multiselect').multiselect("rebuild");
                            $('.competition-multiselect').multiselect("rebuild");
                        });
            });
        });


//        $('#event_group_id').change(function() {
//            if ( ! $(this).val()) {return;}
//            var val = $(this).val();
//
//            if ( ! $.isArray(val) ) {
//                val = [val];
//            }
//
//            $.each(val, function (index, value) {
//                $.get('/admin/tournaments/get-events/' + value)
//                        .done(function(data){
//                            events = data;
//                            $('.events-selector').html(createSelectOptions(data));
//                            $('.sport-multiselect').multiselect("rebuild");
//                            $('.event-multiselect').multiselect("rebuild");
//                            $('.competition-multiselect').multiselect("rebuild");
//
//                            //hacky way to set event
//                            $('.events-selector').each(function() {
//                                var $eventSelect = $(this);
//                                var $date = $(this).parents('.form-group').find('.event-date');
//
//                                $.each(events, function(i, v) {
//                                    if(v.start_date == $date.val()) {
//                                        $eventSelect.val(v.id)
//                                    }
//                                })
//                            });
//                        });
//				$.get('/admin/tournaments/get-markets/' + value)
//                    .done(function(data) {
//                        if( data.length > 0 ) {
//                            var html = $();
//                            $.each(data, function(i,v){
//                                var $row = $("<tr/>");
//                                $row.append("<td/>").text(v.market_type.name);
//                                $row.append("<td/>").text(v.line);
//                                $row.append("<td/>").text(v.tournament_status ? "Yes" : "No");
//                                html.add($row);
//                            });
//
//                            $('#market-table-body').html(html);
//                            $('#markets').slideDown();
//                        } else {
//                            $('#markets').slideUp();
//                        }
//                    });
//            });
//
//        });

        $('.events-selector').change(function(){
            var needle = $(this).val();
            var startDate;
            $.each(events, function(i,v) {
                if(v.id == needle) {
                    startDate = v.start_date;
                    return false;
                }
            });

            var $eventDates = $(this).parents('.form-group').find('.event-date');
            $eventDates.val(startDate);
        });

        $('input[name="jackpot_flag"]').change(function(){
            var $parentTourn = $('#parent-tournament');
            var $sportId = $('#tournament_sport_id');
            console.log($sportId.val());
            if($(this).val() == 1 && $sportId.val() != 0) {

                $.get('/admin/tournaments/get-parent-tournaments/' + $sportId.val())
                        .done(function(data) {
                            $('#parent_tournament_id').html(createSelectOptions(data));
                        });

                $parentTourn.show();
                $parentTourn.removeAttr('disabled');
            } else {
                $parentTourn.hide();
                $parentTourn.attr('disabled', 'disabled');
            }
        });

        $('#tournament_buyin_id').change(function(){

            var $limitApplies = $('#limit_applies');
            if($(this).find("option:selected").text() == '0.00 + 0.00') {
                $limitApplies.show();
            } else {
                $limitApplies.hide();
            }
        });

        $(document).ready(function(){
            var $eventGroupId = $('#event_group_id');
            if($eventGroupId.val() != 0) {
                $eventGroupId.change();
            }

            $('.group-multiselect').multiselect();

            $('#tournament_buyin_id').change();
        })
    </script>

@stop