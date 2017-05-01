        <section id="desktop">
            <header class="ui-state-default ui-state-active">
                {_T string="Activities"}
            </header>
            <div>
                <a id="members" href="{$galette_base_path}gestion_adherents.php" title="{_T string="View, search into and filter member's list"}">{_T string="Members"}</a>

{if $login->isAdmin() or $login->isStaff()}
                {* Amaury: évite qu'un responsable de groupe puisse modifier ou supprimer son propre groupe evol#69 *}
				<a id="groups" href="{$galette_base_path}gestion_groupes.php" title="{_T string="View and manage groups"}">{_T string="Groups"}</a>
				{* fin de l'evol#69 *}
                <a id="contribs" href="{$galette_base_path}gestion_contributions.php?id_adh=all" title="{_T string="View and filter contributions"}">{_T string="Contributions"}</a>
                <a id="transactions" href="{$galette_base_path}gestion_transactions.php" title="{_T string="View and filter transactions"}">{_T string="Transactions"}</a>
                <a id="mailings" href="{$galette_base_path}gestion_mailings.php" title="{_T string="Manage mailings that has been sent"}">{_T string="Mailings"}</a>
                <a id="reminder" href="{$galette_base_path}reminder.php" title="{_T string="Send reminders to late members"}">{_T string="Reminders"}</a>
{/if}
{if $login->isAdmin()}
                <a id="prefs" href="{$galette_base_path}preferences.php" title="{_T string="Set applications preferences (address, website, member's cards configuration, ...)"}">{_T string="Settings"}</a>
                <a id="plugins" href="{$galette_base_path}plugins.php" title="{_T string="Informations about available plugins"}">{_T string="Plugins"}</a>
{/if}
            </div>
        </section>
{if $news|@count > 0}
    <section id="news">
        <header class="ui-state-default ui-state-active">
            {_T string="News"}
        </header>
        <div>
    {foreach from=$news item=post}
            <p>
                <a href="{$post.url}" target="_blank">{$post.title}</a>
            </p>
    {/foreach}
        </div>
    </section>
{/if}
        <p class="center">
            <input type="checkbox" name="show_dashboard" id="show_dashboard" value="1"{if $show_dashboard} checked="checked"{/if}/>
            <label for="show_dashboard">{_T string="Show dashboard on login"}</label>
        </p>
        <script>
            $(function() {
                $('#show_dashboard').change(function(){
                    var _checked = $(this).is(':checked');
                    $.cookie(
                        'show_galette_dashboard',
                        (_checked ? 1 : 0),
                        { expires: 365 }
                    );
                    if ( !_checked ) {
                        var _url = window.location.href;
                        window.location.replace(
                            _url.replace(
                                /\/desktop\.php.*/,
                                '/gestion_adherents.php'
                            )
                        );
                    }
                });
            });
        </script>
