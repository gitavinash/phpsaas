<?php

require_once 'header.php';

?>

<div id='view-search' class='page-view active'>
    <div class='page-header'>
        <h1 class='greeting-title'>Search</h1>
    </div>
    <div class='search-page-container'>
        <form class='search-form' action='/search/' method='get'>
            <div class='search-input-box'>
                <i class='ph ph-magnifying-glass search-icon'></i>
                <input
                    type='text'
                    id='main-search-input'
                    class='search-input'
                    name="q"
                    placeholder='Search news, companies, topics...'
                    autofocus />
                <i class='ph ph-x-circle clear-search-icon' id='clear-search-input' style='display: none;'></i>
            </div>
        </form>

        <!-- Autocomplete Suggestions (Shown when typing) -->
        <div class='search-suggestions' id='search-suggestions' style='display: none;'>
            <h4 class='recent-title'>Suggestions</h4>
            <div class='suggestions-list' id='suggestions-list'>
                <!-- Dynamically populated via JS -->
            </div>
        </div>

        <!-- Default View: Recent or Suggested Topics -->
        <div class='recent-searches' id='search-recent'>
            <div class='recent-header-row'>
                <h4 class='recent-title' id='recent-title'>Recent Searches</h4>
                <button type='button' class='clear-recent-btn' id='clear-recent-btn' style='display: none;'>Clear All</button>
            </div>
            <div class='recent-list' id='recent-list'>
                <!-- Dynamically rendered via JS -->
            </div>
        </div>

    </div>
</div>

<?php

require_once 'footer.php';

?>