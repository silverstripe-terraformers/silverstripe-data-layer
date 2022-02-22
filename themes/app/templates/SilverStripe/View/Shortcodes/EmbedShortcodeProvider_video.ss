<div class="embed__wrapper" style="max-width: {$Arguments.width}px;">
    <div class="embed" style="padding-bottom: {$Arguments.ratioPadding}%;">{$Content}</div>
    <% if $Arguments.caption %>
        <div class="embed__caption-wrapper">
            <p class="embed__caption">{$Arguments.caption}</p>
        </div>
    <% end_if %>
</div>
