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
        array("display_name" => "Tournament Groups", "name" => "tournament-groups"),
        array("display_name" => "User Activity", "name" => "user-activity"),
    ),

    //custom permissions. Permissions which do not fit the resource category
    "custom_permissions" => array(
        array( "name" => "remove_free_credits", "display_name" => "Remove free credits from dormant accounts"),
        array("name" => "add_users_to_tournament", "display_name" => "Add users to tournaments"),
        array("name" => "tournament_settings", "display_name" => "Tournament Settings"),
        array("name" => "remove_users_from_tournament", "display_name" => "Remove users from tournaments"),
        array("name" => "get_user_activity", "display_name" => "Download user activity report"),
    ),

    // non resource routes. Mapping for uri to permissions.
    "custom_routes" => array(
        array("uri" => "sports/{sportId}/competitions", "permission" => "competitions.view"),
        array("uri" => "removeFreeCredits", "permission" => "remove_free_credits"),
        array("uri" => "tournaments/add-users/{tournamentId}", "permission" => "add_users_to_tournament"),
        array("uri" => "tournaments/get-.*", "permission" => "tournaments.view"),
        array("uri" => "sports-list", "permission" => "sports.view"),
        array('uri' => 'tournament-settings', "permission" => "tournament_settings"),
        array('uri' => "tournaments/remove/{tournamentId}/{userId}", "remove_users_from_tournament"),
        array('uri' => 'tournaments/cancel/{tournamentId}', "permission" => 'tournaments.delete'),
        array('uri' => 'user-activity/download', "permission" => "get_user_activity"),
    ),

    // --- SIDEBAR NAVIGATION MENU ---
    "navigation" => array(
        array("name" => "Dashboard", "route" => "admin.dashboard.index", "fa-icon" => "fa-dashboard"),
        array("name" => "Users", "fa-icon" => "fa-user", "children" => array(
            array("name" => "Users", "route" => "admin.users.index"),
            array("name" => "User Activity Report", "route" => "admin.user-activity.index"),
        )),
        array("name" => "Bets", "fa-icon" => "fa-list", "route" => "admin.bets.index"),
        array("name" => "Payments", "fa-icon" => "fa-money", "children" => array(
            array("name" => "Withdrawal Requests", "route" => "admin.withdrawals.index"),
            array("name" => "Account Transactions", "route" => "admin.account-transactions.index"),
            array("name" => "Free Credit Transactions", "route" => "admin.free-credit-transactions.index"),
            array("name" => "Free Credit Management", "route" => "admin.free-credit-management.index"),
        )),
        array("name" => "Tournaments", "fa-icon" => "fa-trophy", "children" => array(
            array("name" => "Tournament List", "fa-icon" => "fa-list", "route" => "admin.tournaments.index"),
            array("name" => "Tournament Event Results", "fa-icon" => "fa-edit", "route" => "admin.tournament-sport-results.index"),
            array("name" => "Tournament Sport Markets", "fa-icon" => "fa-edit", "route" => "admin.tournament-sport-markets.index"),
            array("name" => "Tournament Settings", "fa-icon" => "fa-cog", "url" => "/admin/tournament-settings"),
        )),
        array("name" => "Reports", "route" => "admin.reports.index", "fa-icon" => "fa-file-text"),
        array("name" => "Event Management", "fa-icon" => "fa-list", "children" => array(
            array("name" => "Regions", "route" => "admin.competitionregions.index"),
            array("name" => "Sports", "route" => "admin.sports.index"),
            array("name" => "Base Competitions", "route" => "admin.basecompetitions.index"),
            array("name" => "Competitions", "route" => "admin.competitions.index"),
            array("name" => "Events", "route" => "admin.events.index"),
            array("name" => "Teams", "route" => "admin.teams.index"),
            array("name" => "Players", "route" => "admin.players.index"),
            array("name" => "Markets", "route" => "admin.markets.index"),
            array("name" => "Market Types", "route" => "admin.markettypes.index"),
            array("name" => "Selections", "route" => "admin.selections.index"),
            array("name" => "Prices", "route" => "admin.selectionprices.index"),
            array("name" => "Icons", "route" => "admin.icons.index"),
        )),
        array("name" => "Promotions", "fa-icon" => "fa-money", "route" => "admin.promotions.index"),
        array("name" => "Settings", "fa-icon" => "fa-cogs", "route" => "admin.settings.index"),
        array("name" => "User Groups", "fa-icon" => "fa-user", "route" => "admin.groups.index"),
    ),
);