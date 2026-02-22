@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'FOODYML')
<span style="font-family: Arial, sans-serif; font-size: 30px; font-weight: 900; letter-spacing: 3px; color: #FF6B35;">FOODY</span><span style="font-family: Arial, sans-serif; font-size: 30px; font-weight: 900; letter-spacing: 3px; color: #FFB84D;">ML</span>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
