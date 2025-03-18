<h2>Paragraphs</h2>
<p>A paragraph is one or more continuous pieces of text, separated by blank lines. Do not indent normal paragraphs with spaces or tabs. </p>

<pre class="bg-light p-3 border rounded"><code>This is a paragraph. It has two sentences.

This is another paragraph. It also has two sentences.

</code></pre>

<h2>Line Breaks</h2>
<p>Line breaks inserted in the text are removed. This is because the design philosophy is that the web browser should be responsible for breaking lines according to the screen size. If you want to force a line break, leave two or more spaces at the end of the line and then break the line, which will result in <code>&lt;br&gt;</code>. </p>

<h2>Headings</h2>
<p>You can create headings by placing several <code>#</code> before your text. The number of <code>#</code> corresponds to the level of the heading, and up to six levels of headings are provided. Heading level 1 is a special heading that represents the entire page. Please start using heading level 2.</p>

<pre class="bg-light p-3 border rounded"><code>## Level 2 heading
### Level 3 heading
#### Level 4 heading
##### Level 5 heading
###### Level 6 heading</code></pre>

<p>There are alternative notations for the first two levels. </p>

<pre class="bg-light p-3 border rounded"><code>Level 1 heading
===============

Level 2 heading
---------------</code></pre>

<h2>Quote</h2>
<pre class="bg-light p-3 border rounded"><code>&gt; "This text will be enclosed in an HTML blockquote element.
You can break the text as you like.
Even if you break the text, it will still be treated as a single blockquote element after conversion."</code></pre>

<p>The above will be converted to the following HTML: </p>

<pre class="bg-light p-3 border rounded"><code>&lt;blockquote&gt;
&lt;p&gt;This text will be enclosed in an HTML blockquote element.
You can break the text as much as you like.
Even if you break the text, it will be treated as a single blockquote element after conversion. &lt;/p&gt;
&lt;/blockquote&gt;</code></pre>

<h2>List</h2>

<pre class="bg-light p-3 border rounded"><code>* Unordered list item
* Subitems indented with tabs or 4 spaces
* Another unordered list item

+ Unordered list item
+ Subitems indented with tabs or 4 spaces
+ Another unordered list item

- Unordered list item
- Subitems indented with tabs or 4 spaces
- Another unordered list item

1. Ordered list item
1. Subitems indented with tabs or 4 spaces
2. Another ordered list item</code></pre>

<h2>Code</h2>
<p>If you include code (formatted in monospaced font), the inline code is '<code>`some It is necessary to enclose it in backquotes (U+0060) like this: "code`</code>". </p>

<pre class="bg-light p-3 border rounded"><code>This is a paragraph. It contains `code text` in the sentence. </code></pre>

<p>For code that spans multiple lines, write a tab or four or more spaces at the beginning of the line, or enclose the entire code in three backquotes. </p>

<pre class="bg-light p-3 border rounded"><code> Line 1
Line 2
Line 3</code></pre>

<p>You can optionally specify the language name after the third backquote that indicates the beginning. </p>
<pre class="bg-light p-3 border rounded"><code>```javascript
(() => {
'use strict';

console.log('Hello world');
})();
```</code></pre>

<p>Markdown normally removes line breaks and consecutive spaces, which can break indentation and code layout, but in this case Markdown preserves all whitespace. </p>

<h2>Horizontal Lines</h2>
<p>A horizontal line is created by placing three or more hyphens, asterisks, or underscores on a line. You can have spaces between the hyphens and asterisks. All of the following lines will create horizontal lines: </p>

<pre class="bg-light p-3 border rounded"><code>* * *

***

*****

- - -

---------------------------------------</code></pre>

<h2>Link</h2>
<p>Links can be written as follows: </p>

<pre class="bg-light p-3 border rounded"><code>[Link text](Link address "Link title")</code></pre>

<h2>Emphasis</h2>
<pre class="bg-light p-3 border rounded"><code>*Emphasis* or _Emphasis_

**Strong emphasis** or __Strong emphasis__</code></pre>

<h2>Image</h2>
<p>Images can be embedded as follows. The link starts with <code>!</code>. </p>

<pre class="bg-light p-3 border rounded"><code>![Alt ​​text](/path/to/img.jpg)
![Alt ​​text](/path/to/img.png "Title")</code></pre>

<h2>Table</h2>
<p>The notation is as follows. </p>

<pre class="bg-light p-3 border rounded"><code>| Name | Age | City |
|----------|-----|---------------|
| Alice | 30 | New York |
| Bob | 25 | San Francisco |</code></pre>

<p>Adjusting the formatting with spaces is not required. The following notation is equivalent to the example above. </p>
<pre class="bg-light p-3 border rounded"><code>|Name|Age|City|
|-|-|-|
|Alice|30|NewYork|
|Bob|25|San Francisco|</code></pre>

<p>Adding <code>:</code> to the end of the leftmost cell will make it a row header. </p>
<pre class="bg-light p-3 border rounded"><code>| Name | Age | City |
|----------|-----|---------------|
| Alice :| 30 | New York |
| Bob :| 25 | San Francisco |</code></pre>
