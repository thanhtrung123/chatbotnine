<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#app-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" style="font-weight: bold;" href="{{ route('admin') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                @guest
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{__('admin.header.データ管理')}}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.learning.index') }}">{{__('admin.header.学習データ')}}</a>
                                <a href="{{ route('admin.synonym.index') }}">{{__('admin.header.類義語データ')}}</a>
                                <a href="{{ route('admin.variant.index') }}">{{__('admin.header.異表記データ')}}</a>
                                <a href="{{ route('admin.proper_noun.index') }}">{{__('admin.header.固有名詞')}}</a>
                                @if(config('bot.truth.enabled'))
                                    <a href="{{ route('admin.key_phrase.index') }}">{{__('admin.header.キーフレーズ')}}</a>
                                @endif
                                <a href="{{ route('admin.category.index') }}">{{__('admin.header.カテゴリ')}}</a>
                                <a href="{{ route('admin.scenario.editor') }}">{{__('admin.header.シナリオ管理')}}</a>
                                <a href="{{ route('admin.learning_relation.index') }}">{{__('admin.header.関連質問')}}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{ __('admin.header.応答状況') }} {{__('admin.header.管理')}}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.response_info.index') }}">{{ __('admin.header.応答状況') }}</a>
                                <a href="{{ route('admin.enquete.index') }}">{{ __('admin.header.アンケート') }}</a>
                                <a href="{{ route('admin.report.list') }}">{{__('admin.header.応答状況集計')}}</a>
                            </li>
                        </ul>
                    </li>
                    @role('admin')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{ __('admin.header.システム管理') }}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.user.index') }}">{{ __('admin.header.アカウント情報') }}</a>
                                <a href="{{ route('admin.role.index') }}">{{ __('admin.header.権限情報') }}</a>
                                <a href="{{ route('admin.log.index') }}">{{ __('admin.header.ログ情報') }}</a>
                            </li>
                        </ul>
                    </li>
                    @endrole
                    <li><a href="javascript:void(0);" data-modal='@json(['type'=>'chat'])'>{{__('admin.header.チャット')}}</a></li>
                @endguest
            </ul>
            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                    <li><a href="{{ route('login') }}">{{__('auth.title_login')}}</a></li>
                <!--<li><a href="{{ route('register') }}">Register</a></li>-->
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>
                            {{ Auth::user()->display_name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                    {{ __('admin.header.ログアウト') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-expanded="false" aria-haspopup="true" v-pre>
                       {{__('Language')}}
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{route('language',['en'])}}">En</a>
                            <a class="dropdown-item" href="{{route('language',['ja'])}}">Ja</a>
                            <a class="dropdown-item" href="{{route('language',['vi'])}}">Vi</a>
                        </li>
                    </ul>
                </li>
        </ul>
            
        </div>
    </div>
</nav>