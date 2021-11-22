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
                           aria-expanded="false" aria-haspopup="true" v-pre>データ管理<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.learning.index') }}">{{__('学習データ')}}</a>
                                <a href="{{ route('admin.synonym.index') }}">{{__('類義語データ')}}</a>
                                <a href="{{ route('admin.variant.index') }}">{{__('異表記データ')}}</a>
                                <a href="{{ route('admin.proper_noun.index') }}">{{__('固有名詞')}}</a>
                                @if(config('bot.truth.enabled'))
                                    <a href="{{ route('admin.key_phrase.index') }}">{{__('キーフレーズ')}}</a>
                                @endif
                                <a href="{{ route('admin.category.index') }}">{{__('カテゴリ')}}</a>
                                <a href="{{ route('admin.scenario.editor') }}">{{__('シナリオ管理')}}</a>
                                <a href="{{ route('admin.learning_relation.index') }}">{{__('関連質問')}}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{ __('応答状況') }}管理<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.response_info.index') }}">{{ __('応答状況') }}</a>
                                <a href="{{ route('admin.enquete.index') }}">{{ __('アンケート') }}</a>
                                <a href="{{ route('admin.report.list') }}">{{__('応答状況集計')}}</a>
                            </li>
                        </ul>
                    </li>
                    @role('admin')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{ __('システム管理') }}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.user.index') }}">{{ __('アカウント情報') }}</a>
                                <a href="{{ route('admin.role.index') }}">{{ __('権限情報') }}</a>
                                <a href="{{ route('admin.log.index') }}">{{ __('ログ情報') }}</a>
                            </li>
                        </ul>
                    </li>
                    @endrole
                    <li><a href="javascript:void(0);" data-modal='@json(['type'=>'chat'])'>チャット</a></li>
                @endguest
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                    <li><a href="{{ route('login') }}">ログイン</a></li>
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
                                    {{ __('ログアウト') }}
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
        </div>
    </div>
</nav>