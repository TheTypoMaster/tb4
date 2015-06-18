<?php

return array(

    "prefix" => "admin",

    "permissions" => array("view", "create", "edit", "delete"),

    //resource permissions. Each resource has create, view, edit and delete permissions
    "resources" => array(
        array("display_name" => "Dashboard", "name" => "dashboard", "only" => array("view")),
        array("display_name" => "Users", "name" => "users"),
        array("display_name" => "User Bet Limits", "name" => "users.bet-limits"),
        array("display_name" => "User Bets", "name" => "users.bets", "only" => array("view")),
        array("display_name" => "User Tournaments", "name" => "users.tournaments"),
        array("display_name" => "User Account Transactions", "name" => "users.account-transactions"),
        array("display_name" => "User Free Credit Transactions", "name" => "users.free-credit-transactions"),
        array("display_name" => "User Withdrawals", "name" => "users.withdrawals"),
        array("display_name" => "Bet Limits", "name" => "bet-limits"),
        array("display_name" => "Bets", "name" => "bets"),
        array("display_name" => "Withdrawals", "name" => "withdrawals"),
        array("display_name" => "Account Transactions", "name" => "account-transactions"),
        array("display_name" => "Free Credit Transactions", "name" => "free-credit-transactions"),
        array("display_name" => "Tournaments", "name" => "tournaments"),
        array("display_name" => "Reports", "name" => "reports", "only" => array("view")),
        array("display_name" => "Settings", "name" => "settings"),
        array("display_name" => "Sports", "name" => "sports"),
        array("display_name" => "Competitions", "name" => "competitions"),
        array("display_name" => "Markets", "name" => "markets"),
        array("display_name" => "Market Types", "name" => "markettypes"),
        array("display_name" => "Events", "name" => "events"),
        array("display_name" => "Selections", "name" => "selections"),
        array("display_name" => "Selection Prices", "name" => "selectionprices"),
        array("display_name" => "User Deposit Limits", "name" => "users.deposit-limit"),
        array("display_name" => "Promotions", "name" => "promotions"),
        array("display_name" => "Free Credit Management", "name" => "free-credit-management"),
    ),

    //custom permissions. Permissions which do not fit the resource category
    "custom_permissions" => array(
        array( "name" => "remove_free_credits", "display_name" => "Remove free credits from dormant accounts"),
        array("name" => "add_users_to_tournament", "display_name" => "Add users to tournaments"),
        array("name" => "tournament_settings", "display_name" => "Tournament Settings"),
    ),

    // non resource routes. Mapping for uri to permissions.
    "custom_routes" => array(
        array("uri" => "removeFreeCredits", "permission" => "remove_free_credits"),
        array("uri" => "tournaments/add-users/{tournamentId}", "permission" => "add_users_to_tournament"),
        array("uri" => "tournaments/get-.*", "permission" => "tournaments.view"),
        array('uri' => 'tournament-settings', "permission" => "tournament_settings"),
    ),
);