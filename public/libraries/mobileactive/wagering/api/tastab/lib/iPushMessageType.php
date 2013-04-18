<?php

/**
 * PushMessage constants
 * @author geoff
 *
 */
interface PushMessageType
{
	const SYSTEM_STATUS 			= 'sysStatus';
	const MEETING_LIST 				= 'meetings';
	const EVENT_LIST 				= 'events';
	const RUNNER_LIST 				= 'runners';
	const MULTILEG 					= 'multiLeg';
	const MEETING_MAP 				= 'meMap';
	const MEETING_CONDITION_LIST 	= 'meCondtns';
	const EVENT_STATUS 				= 'evStatus';
	const RUNNER_STATUS 			= 'ruStatus';
	const ODDS_LIST 				= 'winPlace';
	const EXOTIC_LIST 				= 'exotics';
	const RESULT_LIST 				= 'evResults';
	const EVENT_DIVS				= 'evDivs';
}