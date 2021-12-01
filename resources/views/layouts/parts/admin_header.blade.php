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
                           aria-expanded="false" aria-haspopup="true" v-pre>{{__('admin.header.data_management')}}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.learning.index') }}">{{__('admin.header.training_data')}}</a>
                                <a href="{{ route('admin.synonym.index') }}">{{__('admin.header.synonym_data')}}</a>
                                <a href="{{ route('admin.variant.index') }}">{{__('admin.header.variant')}}</a>
                                <a href="{{ route('admin.proper_noun.index') }}">{{__('admin.header.proper_noun')}}</a>
                                @if(config('bot.truth.enabled'))
                                    <a href="{{ route('admin.key_phrase.index') }}">{{__('admin.header.key_phrase')}}</a>
                                @endif
                                <a href="{{ route('admin.category.index') }}">{{__('admin.header.category')}}</a>
                                <a href="{{ route('admin.scenario.editor') }}">{{__('admin.header.scenario_management')}}</a>
                                <a href="{{ route('admin.learning_relation.index') }}">{{__('admin.header.related_questions')}}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{ __('admin.header.Response_stt') }} {{__('admin.header.management')}}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.response_info.index') }}">{{ __('admin.header.Response_stt') }}</a>
                                <a href="{{ route('admin.enquete.index') }}">{{ __('admin.header.questionnaire') }}</a>
                                <a href="{{ route('admin.report.list') }}">{{__('admin.header.sesponse_stt_summary')}}</a>
                            </li>
                        </ul>
                    </li>
                    @role('admin')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false" aria-haspopup="true" v-pre>{{ __('admin.header.system_management') }}<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.user.index') }}">{{ __('admin.header.account_inf') }}</a>
                                <a href="{{ route('admin.role.index') }}">{{ __('admin.header.authority_inf') }}</a>
                                <a href="{{ route('admin.log.index') }}">{{ __('admin.header.log') }}</a>
                            </li>
                        </ul>
                    </li>
                    @endrole
                    <li><a href="javascript:void(0);" data-modal='@json(['type'=>'chat'])'>{{__('admin.header.chat')}}</a></li>
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
                                    {{ __('admin.header.log_out') }}
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