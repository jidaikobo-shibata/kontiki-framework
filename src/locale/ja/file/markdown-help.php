<h2>段落</h2>
<p>段落は1つ以上の連続したテキストであり、空行によって分けられます。通常の段落をスペースやタブでインデントしてはいけません。</p>

<pre class="bg-light p-3 border rounded"><code>これは段落です。2つの文があります。

これは別の段落です。ここにも2つの文があります。</code></pre>

<h2>改行</h2>
<p>テキストに挿入された改行は取り除かれます。これは、画面の大きさに応じて改行を行う処理はWebブラウザが担当すべきであるという設計思想によります。強制的に改行したい場合は、2つ以上のスペースを行末に残した上で改行すると <code>&lt;br&gt;</code> になります。</p>

<h2>見出し</h2>
<p>テキストの前にいくつかの<code>#</code>を置くことで見出しを作ることができます。<code>#</code>の数が見出しのレベルに対応し、見出しのレベルを6まで提供しています。見出しレベル1はページ全体を代表する特別な見出しです。見出しレベル2から使ってください。</p>

<pre class="bg-light p-3 border rounded"><code>## レベル2の見出し
### レベル3の見出し
#### レベル4の見出し
##### レベル5の見出し
###### レベル6の見出し</code></pre>

<p>最初の2つのレベルには代替の記法が存在します。</p>

<pre class="bg-light p-3 border rounded"><code>レベル1の見出し
===============

レベル2の見出し
---------------</code></pre>

<h2>引用</h2>
<pre class="bg-light p-3 border rounded"><code>&gt; "このテキストは、HTMLのblockquote要素に囲まれます。
テキストを好きなように改行することができます。
改行したとしても、変換後はひとつのblockquote要素として扱われます。"</code></pre>

<p>上記は次のようなHTMLに変換されます。</p>

<pre class="bg-light p-3 border rounded"><code>&lt;blockquote&gt;
  &lt;p&gt;このテキストは、HTMLのblockquote要素に囲まれます。
テキストを好きなように改行することができます。
改行したとしても、変換後はひとつのblockquote要素として扱われます。&lt;/p&gt;
&lt;/blockquote&gt;</code></pre>

<h2>リスト</h2>

<pre class="bg-light p-3 border rounded"><code>* 順序無しリストのアイテム
    * サブアイテムはタブもしくは4つのスペースでインデントする
* 順序無しリストの別のアイテム

+ 順序無しリストのアイテム
    + サブアイテムはタブもしくは4つのスペースでインデントする
+ 順序無しリストの別のアイテム

- 順序無しリストのアイテム
    - サブアイテムはタブもしくは4つのスペースでインデントする
- 順序無しリストの別のアイテム

1. 順序付きリストのアイテム
    1. サブアイテムはタブもしくは4つのスペースでインデントする
2. 順序付きリストの別のアイテム</code></pre>

<h2>コード</h2>
<p>コード（等幅フォントで整形される）を含める場合、インラインコードは「<code>`some code`</code>」のようにバッククオート (U+0060) で囲むことにななります。</p>

<pre class="bg-light p-3 border rounded"><code>これは段落です。文中に`コードテキスト`を含みます。</code></pre>

<p>複数行にまたがるコードは、タブもしくは4つ以上のスペースを行頭に書くか、3つずつのバッククオートでコード全体をくくります。</p>

<pre class="bg-light p-3 border rounded"><code>    1行目
    2行目
    3行目</code></pre>

<p>開始を表すバッククオートの3つ目に続けて、任意で言語名を明記することができます。</p>
<pre class="bg-light p-3 border rounded"><code>```javascript
(() => {
  'use strict';

  console.log('Hello world');
})();
```</code></pre>

<p>Markdownは通常、改行や連続したスペースを削除するため、インデントやコードのレイアウトを壊す可能性がありますが、この場合 Markdownは空白をすべて保持します。</p>

<h2>水平線</h2>
<p>1行の中に、3つ以上のハイフンやアスタリスク・アンダースコアだけを並べると水平線が作られます。ハイフンやアスタリスクのあいだには空白を入れることもできます。以下の行はすべて水平線を生成します。</p>

<pre class="bg-light p-3 border rounded"><code>* * *

***

*****

- - -

---------------------------------------</code></pre>

<h2>リンク</h2>
<p>リンクは次のように記述できます。</p>

<pre class="bg-light p-3 border rounded"><code>[リンクのテキスト](リンクのアドレス "リンクのタイトル")</code></pre>

<h2>強調</h2>
<pre class="bg-light p-3 border rounded"><code>*強調* もしくは _強調_

**強い強調** もしくは __強い強調__</code></pre>

<h2>画像</h2>
<p>画像は以下のように埋め込めます。リンクの冒頭に <code>!</code> が付いている形式です。</p>

<pre class="bg-light p-3 border rounded"><code>![Altのテキスト](/path/to/img.jpg)
![Altのテキスト](/path/to/img.png "タイトル")</code></pre>

<h2>表組み</h2>
<p>以下のような記法です。</p>

<pre class="bg-light p-3 border rounded"><code>| Name     | Age | City          |
|----------|-----|---------------|
| Alice    | 30  | New York      |
| Bob      | 25  | San Francisco |</code></pre>

<p>スペースによる体裁の調整は必須ではありません。以下の表記は上の例と等価です。</p>
<pre class="bg-light p-3 border rounded"><code>|Name|Age|City|
|-|-|-|
|Alice|30|NewYork|
|Bob|25|San Francisco|</code></pre>

<p>左端のセルの末尾に<code>:</code>をつけると、行見出しになります。</p>
<pre class="bg-light p-3 border rounded"><code>| Name     | Age | City          |
|----------|-----|---------------|
| Alice   :| 30  | New York      |
| Bob     :| 25  | San Francisco |</code></pre>
