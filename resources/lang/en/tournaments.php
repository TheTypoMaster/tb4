<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Tournaments Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are the default lines which match the
	| Tournaments resource
	|
	*/

	"not_found" => "Tournament id: :tournamentId not found",

	// TICKETS
	"ticket_not_found" => "Tournament ticket not found",
	"existing_ticket" => "You already have a ticket in this tournament",
	"already_bet" => "You've already bet on this tournament",
	"already_started" => "Can't refund a ticket for a tournament which has commenced",
	"refunded_ticket" => "Ticket #:ticketId has been refunded",
	"refund_ticket_problem" => "Ticket #:ticketId could not be refunded",
    "bet_limit_exceeded" => "Maxium bet amount bet per race is",
    "exceed_free_tournament_tickets" => "You have exceeded the maximum number (:number) of free tournaments for this :period",

	// COMMENTS/SLEDGE
	"comment_posted" => "Comment Posted!",
	"comment_issue" => "There was an issue posting your comment",
	"commenting_closed" => "Commenting for this tournament is closed",

    //PRIZES
    "cash_only" => "You've won :amount cash! This amount has been credited to you Account Balance. Remember, to withdraw your cash you need to provide us with the Identification Document.",
    "ticket_only" => "You've won a tikcet to round :parent_tournament",
    "ticket_cash" => "You've won a ticket to round :parent_tournament. You've also won:amount free credit! This amount has been credited to your Free Credit Balance.",
    "free_credit" => "You've won :amount free credit! This amount has been credited to your Free Credit Balance.",
    "ticket_already_registered" => "You've won a tikcet to round :parent_tournament.  You're already registered for that tournament though, so we've credited you with :tourn-amount free credit",
    "ticket_already_registered_cash" => "You've won a tikcet to round :parent_tournament. You're already registered for that tournament though, so we've credited you with :tourn_amount free credit. You've also won:amount free credit! This amount has been credited to your Free Credit Balance.",
	
);