<?php

namespace TopBetta\Repositories;

use Carbon\Carbon;
use DB;
use Response;

/**
 * Description of ReportRepo
 *
 * @author mic
 */
class ReportRepo
{

	public function tournamentForMonthYear($month, $year, $download = false)
	{
		$startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
		$endDate = Carbon::createFromDate($year, $month, 1)->addMonth()->format('Y-m-d');

		$query = DB::table('tbdb_tournament AS t')
				->select(DB::raw("
						t.id AS 'tournament id', t.name, case when ms.name > '' then ms.name else '-' end as state, 
						eg.name AS 'competition', ts.name AS 'sport name', DATE_FORMAT(t.start_date, \"%d/%m/%Y\") AS 'start date', 
						DATE_FORMAT(t.end_date, \"%d/%m/%Y\") AS 'end date', CONCAT('$', FORMAT(t.buy_in/100, 2)) AS buy_in, 
						CONCAT('$', FORMAT(t.entry_fee/100, 2)) AS entry_fee, 
						(SELECT COUNT(*) FROM `tbdb_tournament_ticket` WHERE tournament_id = t.id) as num_entrants,
						IF(t.buy_in * (SELECT COUNT(*) FROM `tbdb_tournament_ticket` WHERE tournament_id = t.id) > t.minimum_prize_pool, 
						CONCAT('$',FORMAT(t.buy_in * (SELECT COUNT(*) FROM `tbdb_tournament_ticket` WHERE tournament_id = t.id)/100, 2)), 
						CONCAT('$',FORMAT(t.minimum_prize_pool/100, 2))) AS 'total prize pool'
						"))
				->leftJoin('tbdb_event_group AS eg', 't.event_group_id', '=', 'eg.id')
				->leftJoin('tbdb_tournament_sport AS ts', 't.tournament_sport_id', '=', 'ts.id')
				->leftJoin('tbdb_meeting_venue AS mv', 'eg.name', '=', 'mv.name')
				->leftJoin('tbdb_meeting_state AS ms', 'mv.meeting_state_id', '=', 'ms.id')
				->where('t.start_date', '>', $startDate)
				->where('t.start_date', '<', $endDate);

		return ($download) ?
				$this->downloadCsv($query->get(), 'Tournaments', $startDate) :
				$query->paginate();
	}

	public function betsForMonthYear($month, $year, $download = false)
	{
		$startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
		$endDate = Carbon::createFromDate($year, $month, 1)->addMonth()->format('Y-m-d');

		$query = DB::table('tbdb_bet AS b')
				->select(DB::raw("
						brs.name AS status, eg.state, eg.name AS venue, eg.type_code, e.number AS race_number, 
						DATE_FORMAT(e.start_date, \"%d/%m/%Y\") AS completed_date, FORMAT(b.bet_amount/100, 2) AS bet_amount,
						FORMAT(act.amount/100, 2) AS return_amount
						"))
				->join('tbdb_bet_result_status AS brs', 'brs.id', '=', 'b.bet_result_status_id')
				->join('tbdb_event_group_event AS ege', 'ege.event_id', '=', 'b.event_id')
				->join('tbdb_event_group AS eg', 'eg.id', '=', 'ege.event_group_id')
				->join('tbdb_event AS e', 'e.id', '=', 'b.event_id')
				->leftJoin('tbdb_account_transaction AS act', 'b.result_transaction_id', '=', 'act.id')
				->where('e.start_date', '>', $startDate)
				->where('e.start_date', '<', $endDate)
				->whereIn('b.bet_result_status_id', array(2, 3, 4))
				->where('b.resulted_flag', 1)
				->orderBy('venue', 'ASC');

		return ($download) ?
				$this->downloadCsv($query->get(), 'Bets', $startDate) :
				$query->paginate();
	}

	/**
	 * Convert the data to CSV and force download
	 * 
	 * @param array/collection $response
	 * @param type $fileName
	 * @param type $date
	 * @return type
	 */
	public function downloadCsv($response, $fileName, $date = '')
	{
		$response = $this->toArrayForCsv($response);
		$csv = '';

		if ($response && count($response)) {
			ob_start();
			$handle = fopen('php://output', 'r+');

			foreach ($response as $r) {
				fputcsv($handle, array_values($r));
			}

			$csv = ob_get_clean();
			fclose($handle);
		}

		return Response::make($csv, 200, array(
					'Content-Type' => 'text/csv',
					'Content-Disposition' => 'attachment;filename=' . $fileName . '-' . $date . '.csv'
		));
	}

	private function toArrayForCsv($data)
	{
		$array = array();
		foreach ($data[0] as $key => $value) {
			$header[] = $key;
		}

		$array[] = $header;

		foreach ($data as $row) {
			$rowData = array();

			foreach ($row as $value) {
				array_push($rowData, $value);
			}

			$array[] = $rowData;
		}

		return $array;
	}

}
