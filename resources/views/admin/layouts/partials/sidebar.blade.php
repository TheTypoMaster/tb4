<div class="navbar-default navbar-static-side" role="navigation">
	<div class="sidebar-collapse">
		<ul class="nav" id="side-menu">
            @foreach($navItems as $item)
                <li @if(array_get($item, 'active')) class="active" @endif>
                    <a href="{{ array_get($item, 'link') ? : '#' }}" >
                        @if( $icon = array_get($item, 'fa-icon', null) )
                            <i class="fa fa-fw {{ $icon }}"></i>
                        @endif

                        {{ array_get($item, 'name') }}

                        @if(array_get($item, 'children') )
                            <span class="fa arrow"></span>
                        @endif
                    </a>
                    @if($children = array_get($item, 'children') )
                        <span class="fa arrow"></span>
                        <ul class="nav nav-second-level">
                            @foreach($children as $childItem)
                                <li>
                                    <a href="{{ array_get($childItem, 'link') ? : '#' }}">
                                        @if( $icon = array_get($childItem, 'fa-icon', null) )
                                            <i class="fa fa-fw {{ $icon }}"></i>
                                        @endif
                                        {{ array_get($childItem, 'name') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>