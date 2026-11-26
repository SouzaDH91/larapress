<ul class="navbar-nav {{ $location === 'footer' ? 'flex-column' : 'flex-row' }} ms-auto mb-2 mb-lg-0 gap-1 align-items-lg-center">
    @foreach($menu->items as $item)
        @php
            $hasChildren = $item->children->isNotEmpty();
            $url = $item->url;
            $isCurrent = request()->fullUrlIs(url($url)) 
                || request()->is(ltrim($url, '/')) 
                || (request()->path() === '/' && ($url === '/' || $url === url('/')));
        @endphp

        @if($hasChildren)
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ $isCurrent ? 'active' : '' }} {{ $item->css_class ?? '' }}" 
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $item->title }}
                </a>
                <ul class="dropdown-menu">
                    @foreach($item->children as $child)
                        @php
                            $childActive = request()->fullUrlIs(url($child->url)) || request()->is(ltrim($child->url, '/'));
                        @endphp
                        <li>
                            <a class="dropdown-item {{ $childActive ? 'active' : '' }} {{ $child->css_class ?? '' }}" 
                               href="{{ $child->url }}" target="{{ $child->target ?? '_self' }}">
                                {{ $child->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link {{ $isCurrent ? 'active' : '' }} {{ $item->css_class ?? '' }}" 
                   href="{{ $item->url }}" target="{{ $item->target ?? '_self' }}">
                    {{ $item->title }}
                </a>
            </li>
        @endif
    @endforeach
</ul>
