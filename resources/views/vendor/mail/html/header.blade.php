@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Crafty Corners')
            <img src="https://cdn.discordapp.com/attachments/718323595855527976/1194170383583170620/Crafty_Corners_logo_minimalist_and_simple_digital_art.png?ex=65af60d6&is=659cebd6&hm=aa45513591eb38ff5a84135572d3fe2a8a8e4a390fda3be76b17a80481b84aa4&"
                class="logo" alt="CraftyCorners">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
