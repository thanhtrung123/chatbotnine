/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: ja.js
 * 	Japanese language file.
 * 
 * File Authors:
 * 		Takashi Yamaguchi (jack@omakase.net)
 */

CKEDITOR.FCKLanguageManager.FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
Dir					: "ltr",

ToolbarCollapse		: "ツールバーを隠す",
ToolbarExpand		: "ツールバーを表示",

// Toolbar Items and Context Menu
Save				: "保存",
NewPage				: "新しいページ",
Preview				: "プレビュー",
Cut					: "切り取り",
Copy				: "コピー",
Paste				: "貼り付け",
PasteText			: "プレーンテキスト貼り付け",
PasteWord			: "ワード文章から貼り付け",
Print				: "印刷",
SelectAll			: "すべて選択",
RemoveFormat		: "フォーマット削除",
InsertLinkLbl		: "リンク",
InsertLink			: "リンク設定",
RemoveLink			: "リンク削除",
Anchor				: "アンカー挿入/編集",
InsertImageLbl		: "画像",
InsertImage			: "画像設定",
InsertFlashLbl		: "Flash",
InsertFlash			: "Flash挿入/編集",
InsertTableLbl		: "テーブル",
InsertTable			: "テーブル挿入/編集",
InsertLineLbl		: "ライン",
InsertLine			: "横罫線",
InsertSpecialCharLbl: "特殊文字",
//InsertSpecialChar	: "特殊文字挿入",
ClearTable	: "テーブルクリーンアップ",
TableDressed : "テーブルの整形",
InsertSmileyLbl		: "絵文字",
//InsertSmiley		: "絵文字挿入",
InsertSmiley		: "テーブルの整形",
About				: "FCKeditorヘルプ",
Bold				: "太字",
Italic				: "斜体",
Underline			: "下線",
StrikeThrough		: "打ち消し線",
Subscript			: "添え字",
Superscript			: "上付き文字",
LeftJustify			: "左揃え",
CenterJustify		: "中央揃え",
RightJustify		: "右揃え",
BlockJustify		: "両端揃え",
DecreaseIndent		: "インデント解除",
IncreaseIndent		: "インデント",
Undo				: "元に戻す",
Redo				: "やり直し",
NumberedListLbl		: "番号付きリスト",				//GD Customize
NumberedList		: "番号付きリストの追加/削除",	//GD Customize
//NumberedListLbl		: "段落番号",
//NumberedList		: "段落番号の追加/削除",
BulletedListLbl		: "箇条書き",
BulletedList		: "",
ShowTableBorders	: "テーブルボーダー表示",
ShowDetails			: "詳細表示",
Style				: "スタイル",
FontFormat			: "フォーマット",
Font				: "フォント",
FontSize			: "サイズ",
TextColor			: "テキスト色",
BGColor				: "背景色",
Source				: "ソース",
Find				: "検索",
Replace				: "置き換え",
SpellCheck			: "スペルチェック",
UniversalKeyboard	: "ユニバーサル・キーボード",
PageBreakLbl		: "改ページ",
PageBreak			: "改ページ挿入",

Form			: "フォーム",
Checkbox		: "チェックボックス",
RadioButton		: "ラジオボタン",
TextField		: "１行テキスト",
Textarea		: "テキストエリア",
HiddenField		: "不可視フィールド",
Button			: "ボタン",
SelectionField	: "選択フィールド",
ImageButton		: "画像ボタン",

FitWindow		: "エディタサイズを最大にします",

// Context Menu
EditLink			: "リンク設定",
CellCM				: "セル",
RowCM				: "行",
ColumnCM			: "カラム",
InsertRow			: "行挿入",
DeleteRows			: "行削除",
InsertColumn		: "列挿入",
DeleteColumns		: "列削除",
InsertCell			: "セル挿入",
DeleteCells			: "セル削除",
MergeCells			: "セル結合",
SplitCell			: "セル分割",
TableDelete			: "テーブル削除",
TableRemove			: "レイアウトテーブル解除",	//070518 ADD
CellProperties		: "セル プロパティ",
TableProperties		: "テーブル プロパティ",
ImageProperties		: "画像設定",
FlashProperties		: "Flash プロパティ",

AnchorProp			: "アンカー プロパティ",
ButtonProp			: "ボタン プロパティ",
CheckboxProp		: "チェックボックス プロパティ",
HiddenFieldProp		: "不可視フィールド プロパティ",
RadioButtonProp		: "ラジオボタン プロパティ",
ImageButtonProp		: "画像ボタン プロパティ",
TextFieldProp		: "１行テキスト プロパティ",
SelectionFieldProp	: "選択フィールド プロパティ",
TextareaProp		: "テキストエリア プロパティ",
FormProp			: "フォーム プロパティ",

CreateDiv			: "Div 作成",
EditDiv				: "Div 編集",
DeleteDiv			: "Div 削除",

YouTube			: "YouTube設定",

FontFormats			: "段落;見出し１;見出し２;見出し３;見出し４;見出し５;見出し６",
//FontFormats			: "段落;整形済みテキスト;連絡先;見出し１;見出し２;見出し３;見出し４;見出し５;見出し６;ブロック",
//FontFormats			: "Normal;Formatted;Address;Heading 1;Heading 2;Heading 3;Heading 4;Heading 5;Heading 6;Paragraph (DIV)",

// Alerts and Messages
ProcessingXHTML		: "XHTML処理中. しばらくお待ちください...",
Done				: "完了",
PasteWordConfirm	: "貼り付けを行うテキストは、ワード文章からコピーされようとしています。貼り付ける前にクリーニングを行いますか？",
NotCompatiblePaste	: "このコマンドはインターネット・エクスプローラーバージョン5.5以上で利用可能です。クリーニングしないで貼り付けを行いますか？",
UnknownToolbarItem	: "未知のツールバー項目 \"%1\"",
UnknownCommand		: "未知のコマンド名 \"%1\"",
NotImplemented		: "コマンドはインプリメントされませんでした。",
UnknownToolbarSet	: "ツールバー設定 \"%1\" 存在しません。",
NoActiveX			: "エラー、警告メッセージなどが発生した場合、ブラウザーのセキュリティ設定によりエディタのいくつかの機能が制限されている可能性があります。セキュリティ設定のオプションで\"ActiveXコントロールとプラグインの実行\"を有効にするにしてください。",
BrowseServerBlocked : "サーバーブラウザーを開くことができませんでした。ポップアップ・ブロック機能が無効になっているか確認してください。",
DialogBlocked		: "ダイアログウィンドウを開くことができませんでした。ポップアップ・ブロック機能が無効になっているか確認してください。",

// Dialogs
DlgBreakTitle		: "#ai#",	//区切り文字
DlgBtnOK			: "OK",
DlgBtnCancel		: "キャンセル",
DlgBtnClose			: "閉じる",
DlgBtnBrowseServer	: "サーバーブラウザー",
DlgAdvancedTag		: "高度な設定",
DlgOpOther			: "<その他>",
DlgInfoTab			: "情報",
DlgAlertUrl			: "URLを挿入してください",

// General Dialogs Labels
DlgGenNotSet		: "<なし>",
DlgGenId			: "Id",
DlgGenLangDir		: "文字表記の方向",
DlgGenLangDirLtr	: "左から右 (LTR)",
DlgGenLangDirRtl	: "右から左 (RTL)",
DlgGenLangCode		: "言語コード",
DlgGenAccessKey		: "アクセスキー",
DlgGenName			: "Name属性",
DlgGenTabIndex		: "タブインデックス",
DlgGenLongDescr		: "longdesc属性(長文説明)",
DlgGenClass			: "スタイルシートクラス",
DlgGenTitle			: "Title属性",
DlgGenContType		: "Content Type属性",
DlgGenLinkCharset	: "リンクcharset属性",
DlgGenStyle			: "スタイルシート",

// Image Dialog
DlgImgTitle			: "画像設定",
DlgImgInfoTab		: "画像 情報",
DlgImgBtnUpload		: "サーバーに送信",
DlgImgURL			: "URL",
DlgImgUpload		: "アップロード",
DlgImgAlt			: "代替テキスト",
DlgImgAlt_Check		: "代替テキストを設定しない",
DlgImgWidth			: "横幅",
DlgImgHeight		: "高さ",
DlgImgLockRatio		: "ロック比率",
DlgBtnResetSize		: "オリジナルサイズに戻す",
DlgImgSizeLocked	: "横幅と高さの比率をロックする",
DlgImgSizeUnLocked	: "横幅と高さの比率をロックしない",
DlgImgBorder		: "枠線",
DlgImgHSpace		: "横間隔",
DlgImgVSpace		: "縦間隔",
DlgImgAlign			: "行揃え",
DlgImgAlignNo		: "指定なし",
DlgImgAlignLeft		: "左",
DlgImgAlignAbsBottom: "下部(絶対的)",
DlgImgAlignAbsMiddle: "中央(絶対的)",
DlgImgAlignBaseline	: "ベースライン",
DlgImgAlignBottom	: "下",
DlgImgAlignMiddle	: "中央",
DlgImgAlignRight	: "右",
DlgImgAlignTextTop	: "テキスト上部",
DlgImgAlignTop		: "上",
DlgImgPreview		: "プレビュー",
DlgImgAlertUrl		: "画像のURLを入力してください。",
DlgImgLinkTab		: "リンク",

// Flash Dialog
DlgFlashTitle		: "Flash プロパティ",
DlgFlashChkPlay		: "再生",
DlgFlashChkLoop		: "ループ再生",
DlgFlashChkMenu		: "Flashメニュー可能",
DlgFlashScale		: "拡大縮小設定",
DlgFlashScaleAll	: "すべて表示",
DlgFlashScaleNoBorder	: "外が見えない様に拡大",
DlgFlashScaleFit	: "上下左右にフィット",

// Link Dialog
DlgLnkWindowTitle	: "リンク設定",
DlgLnkInfoTab		: "リンク 情報",
DlgLnkTargetTab		: "ターゲット",

DlgLnkType			: "リンクタイプ",
DlgLnkTypeURL		: "URL",
DlgLnkTypeAnchor	: "このページのアンカー",
DlgLnkTypeEMail		: "E-Mail",
DlgLnkProto			: "プロトコル",
DlgLnkProtoOther	: "<その他>",
DlgLnkDTL			: "リンク先詳細",
DlgLnkOpDataDTL		: "オープンデータリンク先詳細",
DlgLnkURL			: "外部URL",
DlgLnkAnchorSel		: "アンカーを選択",
DlgLnkAnchorByName	: "アンカー名",
DlgLnkAnchorById	: "エレメントID",
DlgLnkNoAnchors		: "<ドキュメントにおいて利用可能なアンカーはありません。>",
DlgLnkEMail			: "メールアドレス",
DlgLnkEMailSubject	: "件名",
DlgLnkEMailBody		: "本文",
DlgLnkUpload		: "アップロード",
DlgLnkBtnUpload		: "サーバーに送信",
DlgLnkInURL			: "サイト内URL",
DlgLnkFile			: "ファイル",
DlgLnkAnchor		: "アンカー",
DlgLnkTel			: "電話番号",
DlgLnkSummary		: "概要<span class='require'>（必須）</span>",
DlgLnkLicense		: "ライセンス<a target='_blank' href='/shared/system/opendata/license.html'>（説明）</a><span class='require'>（必須）</span>",
DlgLnkDataPoint		: "データ時点<span class='require'>（必須）</span>",
DlgLnkPostingDate	: "掲載日<span class='require'>（必須）</span>",
DlgLnkCategory		: "カテゴリ<span class='require'>（必須）</span>",
DlgLnkDataType		: "データタイプ<a target='_blank' href='/shared/system/opendata/datatype.html'>（説明）</a><span class='require'>（必須）</span>",
DlgLnkKeyword		: "キーワード検索タグ",


//チェックボックスラベル
DlgLnkInURL_Check	: "サイト内ページ",
DlgLnkURL_Check		: "外部ページ",
DlgLnkEMail_Check	: "メール",
DlgLnkFile_Check	: "ファイル",
DlgLnkAnchor_Check	: "現在のページのアンカー",
DlgLnkTel_Check		: "電話番号",
// オープンデータ
// オープンデータファイルの登録
DlgLnkOpData_Check	: "ファイル(オープンデータ)",

DlgLnkTarget		: "ターゲット",
DlgLnkTargetFrame	: "<フレーム>",
DlgLnkTargetPopup	: "<ポップアップウィンドウ>",
DlgLnkTargetBlank	: "新しいウィンドウ (_blank)",
DlgLnkTargetParent	: "親ウィンドウ (_parent)",
DlgLnkTargetSelf	: "同じウィンドウ (_self)",
DlgLnkTargetTop		: "最上位ウィンドウ (_top)",
DlgLnkTargetFrameName	: "目的のフレーム名",
DlgLnkPopWinName	: "ポップアップウィンドウ名",
DlgLnkPopWinFeat	: "ポップアップウィンドウ特徴",
DlgLnkPopResize		: "リサイズ可能",
DlgLnkPopLocation	: "ロケーションバー",
DlgLnkPopMenu		: "メニューバー",
DlgLnkPopScroll		: "スクロールバー",
DlgLnkPopStatus		: "ステータスバー",
DlgLnkPopToolbar	: "ツールバー",
DlgLnkPopFullScrn	: "全画面モード(IE)",
DlgLnkPopDependent	: "開いたウィンドウに連動して閉じる (Netscape)",
DlgLnkPopWidth		: "幅",
DlgLnkPopHeight		: "高さ",
DlgLnkPopLeft		: "左端からの座標で指定",
DlgLnkPopTop		: "上端からの座標で指定",

DlnLnkMsgNoSelTxt	: "リンクを設定するための文字が選択されていません。",
DlnLnkMsgNoUrl		: "リンクURLを入力してください。",
DlnLnkMsgErrUrl		: "リンクURLに不正な値が入力されています。",
DlnLnkMsgNoEMail	: "メールアドレスを入力してください。",
DlnLnkMsgErrEMail	: "メールアドレスに不正な値が入力されています。",
DlnLnkMsgNoAnchor	: "アンカーを選択してください。",
DlnLnkMsgNoFile		: "ファイルパスを入力してください。",
DlnLnkMsgErrExpFile	: "設定できないファイル拡張子が入力されています。",
DlnLnkMsgNoTel		: "電話番号を入力してください。",
DlnLnkMsgErrTel		: "電話番号に不正な値が入力されています。",
DlnLnkMsgExistAKey	: "入力されたアクセスキーは指定されています。",
// オープンデータ
// オープンデータ必須チェック追加
DlnLnkMsgNoSummary	: "概要を入力してください。",
DlnLnkMsgNoLicense	: "ライセンスを選択してください。",
DlnLnkMsgNoPointOfTime		: "データ時点を入力してください。",
DlnLnkMsgNoPublicationDate	: "掲載日を入力してください。",
DlnLnkMsgNoCategory	: "カテゴリを選択してください。",
DlnLnkMsgNoDataType	: "データタイプを選択してください。",

// Color Dialog
DlgColorTitle		: "色選択",
DlgColorBtnClear	: "クリア",
DlgColorHighlight	: "ハイライト",
DlgColorSelected	: "選択色",

// Smiley Dialog
DlgSmileyTitle		: "顔文字挿入",

// Special Character Dialog
DlgSpecialCharTitle	: "特殊文字選択",

// Table Dialog
DlgTableTitle		: "テーブル プロパティ",
DlgTableRows		: "行",
DlgTableColumns		: "列",
DlgTableBorder		: "ボーダーサイズ",
//DlgTableAlign		: "キャプションの整列",	//2007.08.10
DlgTableAlign		: "行揃え",
DlgTableAlignNotSet	: "<なし>",
DlgTableAlignLeft	: "左",
DlgTableAlignCenter	: "中央",
DlgTableAlignRight	: "右",
DlgTableWidth		: "テーブル幅",
DlgTableWidthMsr	: "テーブル幅単位",
DlgCellWidthMsr		: "セル幅単位",
DlgTableWidthPx		: "ピクセル",
DlgTableWidthPc		: "パーセント",
DlgTableHeight		: "テーブル高さ",
DlgTableCellSpace	: "セル内間隔",
DlgTableCellPad		: "セル内余白",
//DlgTableCaption		: "ｷｬﾌﾟｼｮﾝ",	//2007.08.10
DlgTableType		: "テーブルタイプ",
DlgTableCaption		: "キャプション",
DlgTableSummary		: "テーブル目的/構造",

// Table Cell Dialog
DlgCellTitle		: "セル プロパティ",
DlgCellWidth		: "幅",
DlgCellWidthPx		: "ピクセル",
DlgCellWidthPc		: "パーセント",
DlgCellHeight		: "高さ",
DlgCellWordWrap		: "折り返し",
DlgCellWordWrapNotSet	: "<なし>",
DlgCellWordWrapYes	: "Yes",
DlgCellWordWrapNo	: "No",
DlgCellHorAlign		: "セル横の整列",
DlgCellHorAlignNotSet	: "<なし>",
DlgCellHorAlignLeft	: "左",
DlgCellHorAlignCenter	: "中央",
DlgCellHorAlignRight: "右",
DlgCellVerAlign		: "セル縦の整列",
DlgCellVerAlignNotSet	: "<なし>",
DlgCellVerAlignTop	: "上",
DlgCellVerAlignMiddle	: "中央",
DlgCellVerAlignBottom	: "下",
DlgCellVerAlignBaseline	: "ベースライン",
DlgCellRowSpan		: "縦幅(行数)",
DlgCellCollSpan		: "横幅(列数)",
DlgCellBackColor	: "背景色",
DlgCellBorderColor	: "ボーダーカラー",
DlgCellBtnSelect	: "選択...",
// スコープ属性
DlgCellScopeUnSelected	: "選択して下さい。",
DlgCellScopeRow			: "行の見出し（横列）",
DlgCellScopeCol			: "列の見出し（縦列）",

// Find Dialog
DlgFindTitle		: "検索",
DlgFindFindBtn		: "検索",
DlgFindNotFoundMsg	: "指定された文字列は見つかりませんでした。",

// Replace Dialog
DlgReplaceTitle			: "置き換え",
DlgReplaceFindLbl		: "検索する文字列:",
DlgReplaceReplaceLbl	: "置換えする文字列:",
DlgReplaceCaseChk		: "部分一致",
DlgReplaceReplaceBtn	: "置換え",
DlgReplaceReplAllBtn	: "すべて置換え",
DlgReplaceWordChk		: "単語単位で一致",

// Paste Operations / Dialog
PasteErrorPaste	: "ブラウザーのセキュリティ設定によりエディタの貼り付け操作が自動で実行することができません。実行するには手動でキーボードの(Ctrl+V)を使用してください。",
PasteErrorCut	: "ブラウザーのセキュリティ設定によりエディタの切り取り操作が自動で実行することができません。実行するには手動でキーボードの(Ctrl+X)を使用してください。",
PasteErrorCopy	: "ブラウザーのセキュリティ設定によりエディタのコピー操作が自動で実行することができません。実行するには手動でキーボードの(Ctrl+C)を使用してください。",

PasteAsText		: "プレーンテキスト貼り付け",
PasteFromWord	: "ワード文章から貼り付け",

DlgPasteMsg2	: "キーボード(<STRONG>Ctrl+V</STRONG>)を使用して、次の入力エリア内で貼って、<STRONG>OK</STRONG>を押してください。",
DlgPasteIgnoreFont		: "FontタグのFace属性を無視します。",
DlgPasteRemoveStyles	: "スタイル定義を削除します。",
DlgPasteCleanBox		: "入力エリアクリア",

// Color Picker
ColorAutomatic	: "自動",
ColorMoreColors	: "その他の色...",

// Document Properties
DocProps		: "文書 プロパティ",

// Anchor Dialog
DlgAnchorTitle		: "アンカー プロパティ",
DlgAnchorName		: "アンカー名",
DlgAnchorErrorName	: "アンカー名を必ず入力してください。",
DlgAnchorErrorName_num	: "アンカー名の最初の文字に数字を指定することはできません。",
DlgAnchorErrorName_id	: "同一のエレメントIDが指定されています。",
// ▼ADD GD▼
DlgAnchorErrorName_alphabet_char	: "アンカー名の最初の文字は半角英字を指定してください。",
DlgAnchorErrorName_specified_char	: "アンカー名には半角英数字とハイフン（-）とアンダーバー（_）のみの組み合わせを指定してください。",
// ▲ADD GD▲

// Speller Pages Dialog
DlgSpellNotInDic		: "辞書にありません",
DlgSpellChangeTo		: "変更",
DlgSpellBtnIgnore		: "無視",
DlgSpellBtnIgnoreAll	: "すべて無視",
DlgSpellBtnReplace		: "置換",
DlgSpellBtnReplaceAll	: "すべて置換",
DlgSpellBtnUndo			: "やり直し",
DlgSpellNoSuggestions	: "- 該当なし -",
DlgSpellProgress		: "スペルチェック処理中...",
DlgSpellNoMispell		: "スペルチェック完了: スペルの誤りはありませんでした",
DlgSpellNoChanges		: "スペルチェック完了: 語句は変更されませんでした",
DlgSpellOneChange		: "スペルチェック完了: １語句変更されました",
DlgSpellManyChanges		: "スペルチェック完了: %1 語句変更されました",

IeSpellDownload			: "スペルチェッカーがインストールされていません。今すぐダウンロードしますか?",

// Button Dialog
DlgButtonText	: "テキスト (値)",
DlgButtonType	: "タイプ",

// Checkbox and Radio Button Dialogs
DlgCheckboxName		: "名前",
DlgCheckboxValue	: "値",
DlgCheckboxSelected	: "選択済み",

// Form Dialog
DlgFormName		: "フォーム名",
DlgFormAction	: "アクション",
DlgFormMethod	: "メソッド",

// Select Field Dialog
DlgSelectName		: "名前",
DlgSelectValue		: "値",
DlgSelectSize		: "サイズ",
DlgSelectLines		: "行",
DlgSelectChkMulti	: "複数項目選択を許可",
DlgSelectOpAvail	: "利用可能なオプション",
DlgSelectOpText		: "選択項目名",
DlgSelectOpValue	: "選択項目値",
DlgSelectBtnAdd		: "追加",
DlgSelectBtnModify	: "編集",
DlgSelectBtnUp		: "上へ",
DlgSelectBtnDown	: "下へ",
DlgSelectBtnSetValue : "選択した値を設定",
DlgSelectBtnDelete	: "削除",

// Textarea Dialog
DlgTextareaName	: "名前",
DlgTextareaCols	: "列",
DlgTextareaRows	: "行",

// Text Field Dialog
DlgTextName			: "名前",
DlgTextValue		: "値",
DlgTextCharWidth	: "サイズ",
DlgTextMaxChars		: "最大長",
DlgTextType			: "タイプ",
DlgTextTypeText		: "テキスト",
DlgTextTypePass		: "パスワード入力",

// Hidden Field Dialog
DlgHiddenName	: "名前",
DlgHiddenValue	: "値",

// Bulleted List Dialog
BulletedListProp	: "箇条書き プロパティ",
NumberedListProp	: "番号付きリスト プロパティ",	//GD Customize
//NumberedListProp	: "段落番号 プロパティ",
DlgLstType			: "タイプ",
DlgLstTypeCircle	: "白丸",
DlgLstTypeDisc		: "黒丸",
DlgLstTypeSquare	: "四角",
DlgLstTypeNumbers	: "アラビア数字 (1, 2, 3)",
DlgLstTypeLCase		: "英字小文字 (a, b, c)",
DlgLstTypeUCase		: "英字大文字 (A, B, C)",
DlgLstTypeSRoman	: "ローマ数字小文字 (i, ii, iii)",
DlgLstTypeLRoman	: "ローマ数字大文字 (I, II, III)",

// Document Properties Dialog
DlgDocGeneralTab	: "全般",
DlgDocBackTab		: "背景",
DlgDocColorsTab		: "色とマージン",
DlgDocMetaTab		: "メタデータ",

DlgDocPageTitle		: "ページタイトル",
DlgDocLangDir		: "言語文字表記の方向",
DlgDocLangDirLTR	: "左から右に文字表記します(LTR)",
DlgDocLangDirRTL	: "右から左に文字表記します(RTL)",
DlgDocLangCode		: "言語コード",
DlgDocCharSet		: "文字セット符号化",
DlgDocCharSetOther	: "他の文字セット符号化",

DlgDocDocType		: "文書タイプヘッダー",
DlgDocDocTypeOther	: "その他文書タイプヘッダー",
DlgDocIncXHTML		: "XHTML宣言をインクルード",
DlgDocBgColor		: "背景色",
DlgDocBgImage		: "背景画像 URL",
DlgDocBgNoScroll	: "スクロールしない背景",
DlgDocCText			: "テキスト",
DlgDocCLink			: "リンク",
DlgDocCVisited		: "アクセス済みリンク",
DlgDocCActive		: "アクセス中リンク",
DlgDocMargins		: "ページ・マージン",
DlgDocMaTop			: "上部",
DlgDocMaLeft		: "左",
DlgDocMaRight		: "右",
DlgDocMaBottom		: "下部",
DlgDocMeIndex		: "文書のキーワード(カンマ区切り)",
DlgDocMeDescr		: "文書の概要",
DlgDocMeAuthor		: "文書の作者",
DlgDocMeCopy		: "文書の著作権",
DlgDocPreview		: "プレビュー",

// Templates Dialog
//Templates			: "テンプレート(雛形)",
//DlgTemplatesTitle	: "テンプレート内容",
//DlgTemplatesSelMsg	: "エディターで使用するテンプレートを選択してください。<br>(現在のエディタの内容は失われます):",
//DlgTemplatesLoading	: "テンプレート一覧読み込み中. しばらくお待ちください...",
//DlgTemplatesNoTpl	: "(テンプレートが定義されていません)",
//Templates			: "ライブラリ",
//DlgTemplatesTitle	: "ライブラリ設定",
//DlgTemplatesSelMsg	: "編集領域で使用するライブラリを選択してください。",
//DlgTemplatesLoading	: "ライブラリ一覧読み込み中. しばらくお待ちください...",
//DlgTemplatesNoTpl	: "(ライブラリが登録されていません)",
Templates			: "パーツ",
DlgTemplatesTitle	: "パーツ設定",
DlgTemplatesSelMsg	: "編集領域で使用するパーツを選択してください。",
DlgTemplatesLoading	: "パーツ一覧読み込み中. しばらくお待ちください...",
DlgTemplatesNoTpl	: "(パーツが登録されていません)",



// About Dialog
DlgAboutAboutTab	: "バージョン情報",
DlgAboutBrowserInfoTab	: "ブラウザ情報",
DlgAboutLicenseTab	: "ライセンス",
DlgAboutVersion		: "バージョン",
DlgAboutLicense		: "Licensed under the terms of the GNU Lesser General Public License",
DlgAboutInfo		: "より詳しい情報はこちらで",

// Div Dialog
DlgDivGeneralTab	: "全般",
DlgDivAdvancedTab	: "高度な設定",
DlgDivStyle		: "スタイル",
DlgDivInlineStyle	: "インラインスタイル",
DlgDivNoSelTag		: "Div情報の取得に失敗しました。\nDivを選択してください。"
}