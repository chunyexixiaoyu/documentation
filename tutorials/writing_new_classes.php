<!DOCTYPE html>
<html lang="en">
<head>
<title>Documentation - Point Cloud Library (PCL)</title>
</head>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <title>Writing a new PCL class &#8212; PCL 0.0 documentation</title>
    <link rel="stylesheet" href="_static/sphinxdoc.css" type="text/css" />
    <link rel="stylesheet" href="_static/pygments.css" type="text/css" />
    <script type="text/javascript" id="documentation_options" data-url_root="./" src="_static/documentation_options.js"></script>
    <script type="text/javascript" src="_static/jquery.js"></script>
    <script type="text/javascript" src="_static/underscore.js"></script>
    <script type="text/javascript" src="_static/doctools.js"></script>
    <script type="text/javascript" src="_static/language_data.js"></script>
    <link rel="search" title="Search" href="search.php" />
<?php
define('MODX_CORE_PATH', '/var/www/pointclouds.org/core/');
define('MODX_CONFIG_KEY', 'config');

require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');

$snip = $modx->runSnippet("getSiteNavigation", array('id'=>5, 'phLevels'=>'sitenav.level0,sitenav.level1', 'showPageNav'=>'n'));
$chunkOutput = $modx->getChunk("site-header", array('sitenav'=>$snip));
$bodytag = str_replace("[[+showSubmenus:notempty=`", "", $chunkOutput);
$bodytag = str_replace("`]]", "", $bodytag);
echo $bodytag;
echo "\n";
?>
<div id="pagetitle">
<h1>Documentation</h1>
<a id="donate" href="http://www.openperception.org/support/"><img src="/assets/images/donate-button.png" alt="Donate to the Open Perception foundation"/></a>
</div>
<div id="page-content">

  </head><body>

    <div class="document">
      <div class="documentwrapper">
          <div class="body" role="main">
            
  <div class="section" id="writing-a-new-pcl-class">
<span id="writing-new-classes"></span><h1><a class="toc-backref" href="#id42">Writing a new PCL class</a></h1>
<p>Converting code to a PCL-like mentality/syntax for someone that comes in
contact for the first time with our infrastructure might appear difficult, or
raise certain questions.</p>
<p>This short guide is to serve as both a HowTo and a FAQ for writing new PCL
classes, either from scratch, or by adapting old code.</p>
<p>Besides converting your code, this guide also explains some of the advantages
of contributing your code to an already existing open source project. Here, we
advocate for PCL, but you can certainly apply the same ideology to other
similar projects.</p>
<div class="contents topic" id="contents">
<p class="topic-title first">Contents</p>
<ul class="simple">
<li><p><a class="reference internal" href="#writing-a-new-pcl-class" id="id42">Writing a new PCL class</a></p></li>
<li><p><a class="reference internal" href="#advantages-why-contribute" id="id43">Advantages: Why contribute?</a></p></li>
<li><p><a class="reference internal" href="#example-a-bilateral-filter" id="id44">Example: a bilateral filter</a></p></li>
<li><p><a class="reference internal" href="#setting-up-the-structure" id="id45">Setting up the structure</a></p>
<ul>
<li><p><a class="reference internal" href="#bilateral-h" id="id46">bilateral.h</a></p></li>
<li><p><a class="reference internal" href="#bilateral-hpp" id="id47">bilateral.hpp</a></p></li>
<li><p><a class="reference internal" href="#bilateral-cpp" id="id48">bilateral.cpp</a></p></li>
<li><p><a class="reference internal" href="#cmakelists-txt" id="id49">CMakeLists.txt</a></p></li>
</ul>
</li>
<li><p><a class="reference internal" href="#filling-in-the-class-structure" id="id50">Filling in the class structure</a></p>
<ul>
<li><p><a class="reference internal" href="#id3" id="id51">bilateral.cpp</a></p></li>
<li><p><a class="reference internal" href="#id12" id="id52">bilateral.h</a></p></li>
<li><p><a class="reference internal" href="#id27" id="id53">bilateral.hpp</a></p></li>
</ul>
</li>
<li><p><a class="reference internal" href="#taking-advantage-of-other-pcl-concepts" id="id54">Taking advantage of other PCL concepts</a></p>
<ul>
<li><p><a class="reference internal" href="#point-indices" id="id55">Point indices</a></p></li>
<li><p><a class="reference internal" href="#licenses" id="id56">Licenses</a></p></li>
<li><p><a class="reference internal" href="#proper-naming" id="id57">Proper naming</a></p></li>
<li><p><a class="reference internal" href="#code-comments" id="id58">Code comments</a></p></li>
</ul>
</li>
<li><p><a class="reference internal" href="#testing-the-new-class" id="id59">Testing the new class</a></p></li>
</ul>
</div>
</div>
<div class="section" id="advantages-why-contribute">
<h1><a class="toc-backref" href="#id43">Advantages: Why contribute?</a></h1>
<p>The first question that someone might ask and we would like to answer is:</p>
<p><em>Why contribute to PCL, as in what are its advantages?</em></p>
<p>This question assumes you’ve already identified that the set of tools and
libraries that PCL has to offer are useful for your project, so you have already
become an <em>user</em>.</p>
<p>Because open source projects are mostly voluntary efforts, usually with
developers geographically distributed around the world, it’s very common that
the development process has a certain <em>incremental</em>, and <em>iterative</em> flavor.
This means that:</p>
<blockquote>
<div><ul class="simple">
<li><p>it’s impossible for developers to think ahead of all the possible uses a new
piece of code they write might have, but also…</p></li>
<li><p>figuring out solutions for corner cases and applications where bugs might
occur is hard, and might not be desirable to tackle at the beginning, due to
limited resources (mostly a cost function of free time).</p></li>
</ul>
</div></blockquote>
<p>In both cases, everyone has definitely encountered situations where either an
algorithm/method that they need is missing, or an existing one is buggy.
Therefore the next natural step is obvious:</p>
<p><em>change the existing code to fit your application/problem</em>.</p>
<p>While we’re going to discuss how to do that in the next sections, we would
still like to provide an answer for the first question that we raised, namely
“why contribute?”.</p>
<p>In our opinion, there are many advantages. To quote Eric Raymond’s <em>Linus’s
Law</em>: <strong>“given enough eyeballs, all bugs are shallow”</strong>. What this means is
that by opening your code to the world, and allowing others to see it, the
chances of it getting fixed and optimized are higher, especially in the
presence of a dynamic community such as the one that PCL has.</p>
<p>In addition to the above, your contribution might enable, amongst many things:</p>
<blockquote>
<div><ul class="simple">
<li><p>others to create new work based on your code;</p></li>
<li><p>you to learn about new uses (e.g., thinks that you haven’t thought it could be used when you designed it);</p></li>
<li><p>worry-free maintainership (e.g., you can go away for some time, and then return and see your code still working. Others will take care of adapting it to the newest platforms, newest compilers, etc);</p></li>
<li><p>your reputation in the community to grow - everyone likes free stuff (!).</p></li>
</ul>
</div></blockquote>
<p>For most of us, all of the above apply. For others, only some (your mileage
might vary).</p>
</div>
<div class="section" id="example-a-bilateral-filter">
<span id="bilateral-filter-example"></span><h1><a class="toc-backref" href="#id44">Example: a bilateral filter</a></h1>
<p>To illustrate the code conversion process, we selected the following example:
apply a bilateral filter over intensity data from a given input point cloud,
and save the results to disk.</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/point_types.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/io/pcd_io.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>

 <span class="k">typedef</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZI</span> <span class="n">PointT</span><span class="p">;</span>

 <span class="kt">float</span>
 <span class="nf">G</span> <span class="p">(</span><span class="kt">float</span> <span class="n">x</span><span class="p">,</span> <span class="kt">float</span> <span class="n">sigma</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="k">return</span> <span class="n">std</span><span class="o">::</span><span class="n">exp</span> <span class="p">(</span><span class="o">-</span> <span class="p">(</span><span class="n">x</span><span class="o">*</span><span class="n">x</span><span class="p">)</span><span class="o">/</span><span class="p">(</span><span class="mi">2</span><span class="o">*</span><span class="n">sigma</span><span class="o">*</span><span class="n">sigma</span><span class="p">));</span>
 <span class="p">}</span>

 <span class="kt">int</span>
 <span class="nf">main</span> <span class="p">(</span><span class="kt">int</span> <span class="n">argc</span><span class="p">,</span> <span class="kt">char</span> <span class="o">*</span><span class="n">argv</span><span class="p">[])</span>
 <span class="p">{</span>
   <span class="n">std</span><span class="o">::</span><span class="n">string</span> <span class="n">incloudfile</span> <span class="o">=</span> <span class="n">argv</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
   <span class="n">std</span><span class="o">::</span><span class="n">string</span> <span class="n">outcloudfile</span> <span class="o">=</span> <span class="n">argv</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
   <span class="kt">float</span> <span class="n">sigma_s</span> <span class="o">=</span> <span class="n">atof</span> <span class="p">(</span><span class="n">argv</span><span class="p">[</span><span class="mi">3</span><span class="p">]);</span>
   <span class="kt">float</span> <span class="n">sigma_r</span> <span class="o">=</span> <span class="n">atof</span> <span class="p">(</span><span class="n">argv</span><span class="p">[</span><span class="mi">4</span><span class="p">]);</span>

   <span class="c1">// Load cloud</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">cloud</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span><span class="p">);</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">io</span><span class="o">::</span><span class="n">loadPCDFile</span> <span class="p">(</span><span class="n">incloudfile</span><span class="p">.</span><span class="n">c_str</span> <span class="p">(),</span> <span class="o">*</span><span class="n">cloud</span><span class="p">);</span>
   <span class="kt">int</span> <span class="n">pnumber</span> <span class="o">=</span> <span class="p">(</span><span class="kt">int</span><span class="p">)</span><span class="n">cloud</span><span class="o">-&gt;</span><span class="n">size</span> <span class="p">();</span>

   <span class="c1">// Output Cloud = Input Cloud</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="n">outcloud</span> <span class="o">=</span> <span class="o">*</span><span class="n">cloud</span><span class="p">;</span>

   <span class="c1">// Set up KDTree</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">tree</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span><span class="p">);</span>
   <span class="n">tree</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">cloud</span><span class="p">);</span>

   <span class="c1">// Neighbors containers</span>
   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">k_indices</span><span class="p">;</span>
   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="n">k_distances</span><span class="p">;</span>

   <span class="c1">// Main Loop</span>
   <span class="k">for</span> <span class="p">(</span><span class="kt">int</span> <span class="n">point_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">point_id</span> <span class="o">&lt;</span> <span class="n">pnumber</span><span class="p">;</span> <span class="o">++</span><span class="n">point_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="kt">float</span> <span class="n">BF</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>
     <span class="kt">float</span> <span class="n">W</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>

     <span class="n">tree</span><span class="o">-&gt;</span><span class="n">radiusSearch</span> <span class="p">(</span><span class="n">point_id</span><span class="p">,</span> <span class="mi">2</span> <span class="o">*</span> <span class="n">sigma_s</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>

     <span class="c1">// For each neighbor</span>
     <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">n_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">n_id</span> <span class="o">&lt;</span> <span class="n">k_indices</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">n_id</span><span class="p">)</span>
     <span class="p">{</span>
       <span class="kt">float</span> <span class="n">id</span> <span class="o">=</span> <span class="n">k_indices</span><span class="p">.</span><span class="n">at</span> <span class="p">(</span><span class="n">n_id</span><span class="p">);</span>
       <span class="kt">float</span> <span class="n">dist</span> <span class="o">=</span> <span class="n">sqrt</span> <span class="p">(</span><span class="n">k_distances</span><span class="p">.</span><span class="n">at</span> <span class="p">(</span><span class="n">n_id</span><span class="p">));</span>
       <span class="kt">float</span> <span class="n">intensity_dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">abs</span> <span class="p">(</span><span class="n">cloud</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">point_id</span><span class="p">].</span><span class="n">intensity</span> <span class="o">-</span> <span class="n">cloud</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">);</span>

       <span class="kt">float</span> <span class="n">w_a</span> <span class="o">=</span> <span class="n">G</span> <span class="p">(</span><span class="n">dist</span><span class="p">,</span> <span class="n">sigma_s</span><span class="p">);</span>
       <span class="kt">float</span> <span class="n">w_b</span> <span class="o">=</span> <span class="n">G</span> <span class="p">(</span><span class="n">intensity_dist</span><span class="p">,</span> <span class="n">sigma_r</span><span class="p">);</span>
       <span class="kt">float</span> <span class="n">weight</span> <span class="o">=</span> <span class="n">w_a</span> <span class="o">*</span> <span class="n">w_b</span><span class="p">;</span>

       <span class="n">BF</span> <span class="o">+=</span> <span class="n">weight</span> <span class="o">*</span> <span class="n">cloud</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">;</span>
       <span class="n">W</span> <span class="o">+=</span> <span class="n">weight</span><span class="p">;</span>
     <span class="p">}</span>

     <span class="n">outcloud</span><span class="p">.</span><span class="n">points</span><span class="p">[</span><span class="n">point_id</span><span class="p">].</span><span class="n">intensity</span> <span class="o">=</span> <span class="n">BF</span> <span class="o">/</span> <span class="n">W</span><span class="p">;</span>
   <span class="p">}</span>

   <span class="c1">// Save filtered output</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">io</span><span class="o">::</span><span class="n">savePCDFile</span> <span class="p">(</span><span class="n">outcloudfile</span><span class="p">.</span><span class="n">c_str</span> <span class="p">(),</span> <span class="n">outcloud</span><span class="p">);</span>
   <span class="k">return</span> <span class="p">(</span><span class="mi">0</span><span class="p">);</span>
 <span class="p">}</span>
</pre></div>
</td></tr></table></div>
<dl class="simple">
<dt>The presented code snippet contains:</dt><dd><ul class="simple">
<li><p>an I/O component: lines 21-27 (reading data from disk), and 64 (writing data to disk)</p></li>
<li><p>an initialization component: lines 29-35 (setting up a search method for nearest neighbors using a KdTree)</p></li>
<li><p>the actual algorithmic component: lines 7-11 and 37-61</p></li>
</ul>
</dd>
</dl>
<p>Our goal here is to convert the algorithm given into an useful PCL class so that it can be reused elsewhere.</p>
</div>
<div class="section" id="setting-up-the-structure">
<h1><a class="toc-backref" href="#id45">Setting up the structure</a></h1>
<div class="admonition note">
<p class="admonition-title">Note</p>
<p>If you’re not familiar with the PCL file structure already, please go ahead
and read the <a class="reference external" href="http://www.pointclouds.org/documentation/advanced/pcl_style_guide.php">PCL C++ Programming Style Guide</a> to
familiarize yourself with the concepts.</p>
</div>
<p>There’re two different ways we could set up the structure: i) set up the code
separately, as a standalone PCL class, but outside of the PCL code tree; or ii)
set up the files directly in the PCL code tree. Since our assumption is that
the end result will be contributed back to PCL, it’s best to concentrate on the
latter, also because it is a bit more complex (i.e., it involves a few
additional steps). You can obviously repeat these steps with the former case as
well, with the exception that you don’t need the files copied in the PCL tree,
nor you need the fancier <em>cmake</em> logic.</p>
<p>Assuming that we want the new algorithm to be part of the PCL Filtering library, we will begin by creating 3 different files under filters:</p>
<blockquote>
<div><ul class="simple">
<li><p><em>include/pcl/filters/bilateral.h</em> - will contain all definitions;</p></li>
<li><p><em>include/pcl/filters/impl/bilateral.hpp</em> - will contain the templated implementations;</p></li>
<li><p><em>src/bilateral.cpp</em> - will contain the explicit template instantiations <a class="footnote-reference brackets" href="#id2" id="id1">*</a>.</p></li>
</ul>
</div></blockquote>
<p>We also need a name for our new class. Let’s call it <cite>BilateralFilter</cite>.</p>
<dl class="footnote brackets">
<dt class="label" id="id2"><span class="brackets"><a class="fn-backref" href="#id1">*</a></span></dt>
<dd><p>Some PCL filter algorithms provide two implementations: one for PointCloud&lt;T&gt; types and another one operating on legacy PCLPointCloud2 types. This is no longer required.</p>
</dd>
</dl>
<div class="section" id="bilateral-h">
<h2><a class="toc-backref" href="#id46">bilateral.h</a></h2>
<p>As previously mentioned, the <em>bilateral.h</em> header file will contain all the
definitions pertinent to the <cite>BilateralFilter</cite> class. Here’s a minimal
skeleton:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#ifndef PCL_FILTERS_BILATERAL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/filter.h&gt;</span><span class="cp"></span>

 <span class="k">namespace</span> <span class="n">pcl</span>
 <span class="p">{</span>
   <span class="k">template</span><span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span>
   <span class="k">class</span> <span class="nc">BilateralFilter</span> <span class="o">:</span> <span class="k">public</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span>
   <span class="p">{</span>
   <span class="p">};</span>
 <span class="p">}</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_H_</span>
</pre></div>
</td></tr></table></div>
</div>
<div class="section" id="bilateral-hpp">
<h2><a class="toc-backref" href="#id47">bilateral.hpp</a></h2>
<p>While we’re at it, let’s set up two skeleton <em>bilateral.hpp</em> and
<em>bilateral.cpp</em> files as well. First, <em>bilateral.hpp</em>:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#ifndef PCL_FILTERS_BILATERAL_IMPL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_IMPL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_IMPL_H_</span>
</pre></div>
</td></tr></table></div>
<p>This should be straightforward. We haven’t declared any methods for
<cite>BilateralFilter</cite> yet, therefore there is no implementation.</p>
</div>
<div class="section" id="bilateral-cpp">
<h2><a class="toc-backref" href="#id48">bilateral.cpp</a></h2>
<p>Let’s write <em>bilateral.cpp</em> too:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/impl/bilateral.hpp&gt;</span><span class="cp"></span>
</pre></div>
</td></tr></table></div>
<p>Because we are writing templated code in PCL (1.x) where the template parameter
is a point type (see <a class="reference internal" href="adding_custom_ptype.php#adding-custom-ptype"><span class="std std-ref">Adding your own custom PointT type</span></a>), we want to explicitly
instantiate the most common use cases in <em>bilateral.cpp</em>, so that users don’t
have to spend extra cycles when compiling code that uses our
<cite>BilateralFilter</cite>. To do this, we need to access both the header
(<em>bilateral.h</em>) and the implementations (<em>bilateral.hpp</em>).</p>
</div>
<div class="section" id="cmakelists-txt">
<h2><a class="toc-backref" href="#id49">CMakeLists.txt</a></h2>
<p>Let’s add all the files to the PCL Filtering <em>CMakeLists.txt</em> file, so we can
enable the build.</p>
<div class="highlight-cmake notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20</pre></div></td><td class="code"><div class="highlight"><pre><span></span> # Find &quot;set (srcs&quot;, and add a new entry there, e.g.,
 set (srcs
      src/conditional_removal.cpp
      # ...
      src/bilateral.cpp)
      )

 # Find &quot;set (incs&quot;, and add a new entry there, e.g.,
 set (incs
      include pcl/${SUBSYS_NAME}/conditional_removal.h
      # ...
      include pcl/${SUBSYS_NAME}/bilateral.h
      )

 # Find &quot;set (impl_incs&quot;, and add a new entry there, e.g.,
 set (impl_incs
      include/pcl/${SUBSYS_NAME}/impl/conditional_removal.hpp
      # ...
      include/pcl/${SUBSYS_NAME}/impl/bilateral.hpp
      )
</pre></div>
</td></tr></table></div>
</div>
</div>
<div class="section" id="filling-in-the-class-structure">
<span id="filling"></span><h1><a class="toc-backref" href="#id50">Filling in the class structure</a></h1>
<p>If you correctly edited all the files above, recompiling PCL using the new
filter classes in place should work without problems. In this section, we’ll
begin filling in the actual code in each file. Let’s start with the
<em>bilateral.cpp</em> file, as its content is the shortest.</p>
<div class="section" id="id3">
<h2><a class="toc-backref" href="#id51">bilateral.cpp</a></h2>
<p>As previously mentioned, we’re going to explicitly instantiate and
<em>precompile</em> a number of templated specializations for the <cite>BilateralFilter</cite>
class. While this might lead to an increased compilation time for the PCL
Filtering library, it will save users the pain of processing and compiling the
templates on their end, when they use the class in code they write. The
simplest possible way to do this would be to declare each instance that we want
to precompile by hand in the <em>bilateral.cpp</em> file as follows:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6
7
8</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/point_types.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/impl/bilateral.hpp&gt;</span><span class="cp"></span>

 <span class="k">template</span> <span class="k">class</span> <span class="nc">PCL_EXPORTS</span> <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span><span class="o">&gt;</span><span class="p">;</span>
 <span class="k">template</span> <span class="k">class</span> <span class="nc">PCL_EXPORTS</span> <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZI</span><span class="o">&gt;</span><span class="p">;</span>
 <span class="k">template</span> <span class="k">class</span> <span class="nc">PCL_EXPORTS</span> <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZRGB</span><span class="o">&gt;</span><span class="p">;</span>
 <span class="c1">// ...</span>
</pre></div>
</td></tr></table></div>
<p>However, this becomes cumbersome really fast, as the number of point types PCL
supports grows. Maintaining this list up to date in multiple files in PCL is
also painful. Therefore, we are going to use a special macro called
<cite>PCL_INSTANTIATE</cite> and change the above code as follows:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/point_types.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/impl/instantiate.hpp&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/impl/bilateral.hpp&gt;</span><span class="cp"></span>

 <span class="n">PCL_INSTANTIATE</span><span class="p">(</span><span class="n">BilateralFilter</span><span class="p">,</span> <span class="n">PCL_XYZ_POINT_TYPES</span><span class="p">);</span>
</pre></div>
</td></tr></table></div>
<p>This example, will instantiate a <cite>BilateralFilter</cite> for all XYZ point types
defined in the <em>point_types.h</em> file (see
<a href="#id4"><span class="problematic" id="id5">:pcl:`PCL_XYZ_POINT_TYPES&lt;PCL_XYZ_POINT_TYPES&gt;`</span></a> for more information).</p>
<p>By looking closer at the code presented in <a class="reference internal" href="#bilateral-filter-example"><span class="std std-ref">Example: a bilateral filter</span></a>, we
notice constructs such as <cite>cloud-&gt;points[point_id].intensity</cite>. This indicates
that our filter expects the presence of an <strong>intensity</strong> field in the point
type. Because of this, using <strong>PCL_XYZ_POINT_TYPES</strong> won’t work, as not all the
types defined there have intensity data present. In fact, it’s easy to notice
that only two of the types contain intensity, namely:
<a href="#id6"><span class="problematic" id="id7">:pcl:`PointXYZI&lt;pcl::PointXYZI&gt;`</span></a> and
<a href="#id8"><span class="problematic" id="id9">:pcl:`PointXYZINormal&lt;pcl::PointXYZINormal&gt;`</span></a>. We therefore replace
<strong>PCL_XYZ_POINT_TYPES</strong> and the final <em>bilateral.cpp</em> file becomes:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/point_types.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/impl/instantiate.hpp&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/impl/bilateral.hpp&gt;</span><span class="cp"></span>

 <span class="n">PCL_INSTANTIATE</span><span class="p">(</span><span class="n">BilateralFilter</span><span class="p">,</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZI</span><span class="p">)(</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZINormal</span><span class="p">));</span>
</pre></div>
</td></tr></table></div>
<p>Note that at this point we haven’t declared the PCL_INSTANTIATE template for
<cite>BilateralFilter</cite>, nor did we actually implement the pure virtual functions in
the abstract class <a href="#id10"><span class="problematic" id="id11">:pcl:`pcl::Filter&lt;pcl::Filter&gt;`</span></a> so attempting to compile the
code will result in errors like:</p>
<div class="highlight-default notranslate"><div class="highlight"><pre><span></span>filters/src/bilateral.cpp:6:32: error: expected constructor, destructor, or type conversion before ‘(’ token
</pre></div>
</div>
</div>
<div class="section" id="id12">
<h2><a class="toc-backref" href="#id52">bilateral.h</a></h2>
<p>We begin filling the <cite>BilateralFilter</cite> class by first declaring the
constructor, and its member variables. Because the bilateral filtering
algorithm has two parameters, we will store these as class members, and
implement setters and getters for them, to be compatible with the PCL 1.x API
paradigms.</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="p">...</span>
 <span class="k">namespace</span> <span class="n">pcl</span>
 <span class="p">{</span>
   <span class="k">template</span><span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span>
   <span class="k">class</span> <span class="nc">BilateralFilter</span> <span class="o">:</span> <span class="k">public</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span>
   <span class="p">{</span>
     <span class="k">public</span><span class="o">:</span>
       <span class="n">BilateralFilter</span> <span class="p">()</span> <span class="o">:</span> <span class="n">sigma_s_</span> <span class="p">(</span><span class="mi">0</span><span class="p">),</span>
                            <span class="n">sigma_r_</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="n">numeric_limits</span><span class="o">&lt;</span><span class="kt">double</span><span class="o">&gt;::</span><span class="n">max</span> <span class="p">())</span>
       <span class="p">{</span>
       <span class="p">}</span>

       <span class="kt">void</span>
       <span class="n">setSigmaS</span> <span class="p">(</span><span class="k">const</span> <span class="kt">double</span> <span class="n">sigma_s</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">sigma_s_</span> <span class="o">=</span> <span class="n">sigma_s</span><span class="p">;</span>
       <span class="p">}</span>

       <span class="kt">double</span>
       <span class="n">getSigmaS</span> <span class="p">()</span> <span class="k">const</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">sigma_s_</span><span class="p">);</span>
       <span class="p">}</span>

       <span class="kt">void</span>
       <span class="n">setSigmaR</span> <span class="p">(</span><span class="k">const</span> <span class="kt">double</span> <span class="n">sigma_r</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">sigma_r_</span> <span class="o">=</span> <span class="n">sigma_r</span><span class="p">;</span>
       <span class="p">}</span>

       <span class="kt">double</span>
       <span class="n">getSigmaR</span> <span class="p">()</span> <span class="k">const</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">sigma_r_</span><span class="p">);</span>
       <span class="p">}</span>

     <span class="k">private</span><span class="o">:</span>
       <span class="kt">double</span> <span class="n">sigma_s_</span><span class="p">;</span>
       <span class="kt">double</span> <span class="n">sigma_r_</span><span class="p">;</span>
   <span class="p">};</span>
 <span class="p">}</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_H_</span>
</pre></div>
</td></tr></table></div>
<p>Nothing out of the ordinary so far, except maybe lines 8-9, where we gave some
default values to the two parameters. Because our class inherits from
<a href="#id13"><span class="problematic" id="id14">:pcl:`pcl::Filter&lt;pcl::Filter&gt;`</span></a>, and that inherits from
<a href="#id15"><span class="problematic" id="id16">:pcl:`pcl::PCLBase&lt;pcl::PCLBase&gt;`</span></a>, we can make use of the
<a href="#id17"><span class="problematic" id="id18">:pcl:`setInputCloud&lt;pcl::PCLBase::setInputCloud&gt;`</span></a> method to pass the input data
to our algorithm (stored as <a href="#id19"><span class="problematic" id="id20">:pcl:`input_&lt;pcl::PCLBase::input_&gt;`</span></a>). We therefore
add an <cite>using</cite> declaration as follows:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6
7
8</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="p">...</span>
   <span class="k">template</span><span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span>
   <span class="k">class</span> <span class="nc">BilateralFilter</span> <span class="o">:</span> <span class="k">public</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span>
   <span class="p">{</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">input_</span><span class="p">;</span>
     <span class="k">public</span><span class="o">:</span>
       <span class="n">BilateralFilter</span> <span class="p">()</span> <span class="o">:</span> <span class="n">sigma_s_</span> <span class="p">(</span><span class="mi">0</span><span class="p">),</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
<p>This will make sure that our class has access to the member variable <cite>input_</cite>
without typing the entire construct. Next, we observe that each class that
inherits from <a href="#id21"><span class="problematic" id="id22">:pcl:`pcl::Filter&lt;pcl::Filter&gt;`</span></a> must inherit a
<a href="#id23"><span class="problematic" id="id24">:pcl:`applyFilter&lt;pcl::Filter::applyFilter&gt;`</span></a> method. We therefore define:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="p">...</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">input_</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">PointCloud</span> <span class="n">PointCloud</span><span class="p">;</span>

     <span class="k">public</span><span class="o">:</span>
       <span class="n">BilateralFilter</span> <span class="p">()</span> <span class="o">:</span> <span class="n">sigma_s_</span> <span class="p">(</span><span class="mi">0</span><span class="p">),</span>
                            <span class="n">sigma_r_</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="n">numeric_limits</span><span class="o">&lt;</span><span class="kt">double</span><span class="o">&gt;::</span><span class="n">max</span> <span class="p">())</span>
       <span class="p">{</span>
       <span class="p">}</span>

       <span class="kt">void</span>
       <span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">);</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
<p>The implementation of <cite>applyFilter</cite> will be given in the <em>bilateral.hpp</em> file
later. Line 3 constructs a typedef so that we can use the type <cite>PointCloud</cite>
without typing the entire construct.</p>
<p>Looking at the original code from section <a class="reference internal" href="#bilateral-filter-example"><span class="std std-ref">Example: a bilateral filter</span></a>, we
notice that the algorithm consists of applying the same operation to every
point in the cloud. To keep the <cite>applyFilter</cite> call clean, we therefore define
method called <cite>computePointWeight</cite> whose implementation will contain the corpus
defined in between lines 45-58:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6
7</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="p">...</span>
       <span class="kt">void</span>
       <span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">);</span>

       <span class="kt">double</span>
       <span class="nf">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span> <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span> <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">);</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
<p>In addition, we notice that lines 29-31 and 43 from section
<a class="reference internal" href="#bilateral-filter-example"><span class="std std-ref">Example: a bilateral filter</span></a> construct a <a href="#id25"><span class="problematic" id="id26">:pcl:`KdTree&lt;pcl::KdTree&gt;`</span></a>
structure for obtaining the nearest neighbors for a given point. We therefore
add:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree.h&gt;</span><span class="cp"></span>
 <span class="p">...</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">input_</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">PointCloud</span> <span class="n">PointCloud</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTree</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">KdTreePtr</span><span class="p">;</span>

   <span class="k">public</span><span class="o">:</span>
 <span class="p">...</span>

     <span class="kt">void</span>
     <span class="n">setSearchMethod</span> <span class="p">(</span><span class="k">const</span> <span class="n">KdTreePtr</span> <span class="o">&amp;</span><span class="n">tree</span><span class="p">)</span>
     <span class="p">{</span>
       <span class="n">tree_</span> <span class="o">=</span> <span class="n">tree</span><span class="p">;</span>
     <span class="p">}</span>

   <span class="k">private</span><span class="o">:</span>
 <span class="p">...</span>
     <span class="n">KdTreePtr</span> <span class="n">tree_</span><span class="p">;</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
<p>Finally, we would like to add the kernel method (<cite>G (float x, float sigma)</cite>)
inline so that we speed up the computation of the filter. Because the method is
only useful within the context of the algorithm, we will make it private. The
header file becomes:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66
67
68
69
70
71
72
73
74</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#ifndef PCL_FILTERS_BILATERAL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/filter.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree.h&gt;</span><span class="cp"></span>

 <span class="k">namespace</span> <span class="n">pcl</span>
 <span class="p">{</span>
   <span class="k">template</span><span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span>
   <span class="k">class</span> <span class="nc">BilateralFilter</span> <span class="o">:</span> <span class="k">public</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span>
   <span class="p">{</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">input_</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">PointCloud</span> <span class="n">PointCloud</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTree</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">KdTreePtr</span><span class="p">;</span>

     <span class="k">public</span><span class="o">:</span>
       <span class="n">BilateralFilter</span> <span class="p">()</span> <span class="o">:</span> <span class="n">sigma_s_</span> <span class="p">(</span><span class="mi">0</span><span class="p">),</span>
                            <span class="n">sigma_r_</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="n">numeric_limits</span><span class="o">&lt;</span><span class="kt">double</span><span class="o">&gt;::</span><span class="n">max</span> <span class="p">())</span>
       <span class="p">{</span>
       <span class="p">}</span>


       <span class="kt">void</span>
       <span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">);</span>

       <span class="kt">double</span>
       <span class="nf">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span> <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span> <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">);</span>

       <span class="kt">void</span>
       <span class="nf">setSigmaS</span> <span class="p">(</span><span class="k">const</span> <span class="kt">double</span> <span class="n">sigma_s</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">sigma_s_</span> <span class="o">=</span> <span class="n">sigma_s</span><span class="p">;</span>
       <span class="p">}</span>

       <span class="kt">double</span>
       <span class="nf">getSigmaS</span> <span class="p">()</span> <span class="k">const</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">sigma_s_</span><span class="p">);</span>
       <span class="p">}</span>

       <span class="kt">void</span>
       <span class="nf">setSigmaR</span> <span class="p">(</span><span class="k">const</span> <span class="kt">double</span> <span class="n">sigma_r</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">sigma_r_</span> <span class="o">=</span> <span class="n">sigma_r</span><span class="p">;</span>
       <span class="p">}</span>

       <span class="kt">double</span>
       <span class="nf">getSigmaR</span> <span class="p">()</span> <span class="k">const</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">sigma_r_</span><span class="p">);</span>
       <span class="p">}</span>

       <span class="kt">void</span>
       <span class="nf">setSearchMethod</span> <span class="p">(</span><span class="k">const</span> <span class="n">KdTreePtr</span> <span class="o">&amp;</span><span class="n">tree</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">tree_</span> <span class="o">=</span> <span class="n">tree</span><span class="p">;</span>
       <span class="p">}</span>


     <span class="k">private</span><span class="o">:</span>

       <span class="kr">inline</span> <span class="kt">double</span>
       <span class="n">kernel</span> <span class="p">(</span><span class="kt">double</span> <span class="n">x</span><span class="p">,</span> <span class="kt">double</span> <span class="n">sigma</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="n">exp</span> <span class="p">(</span><span class="o">-</span> <span class="p">(</span><span class="n">x</span><span class="o">*</span><span class="n">x</span><span class="p">)</span><span class="o">/</span><span class="p">(</span><span class="mi">2</span><span class="o">*</span><span class="n">sigma</span><span class="o">*</span><span class="n">sigma</span><span class="p">)));</span>
       <span class="p">}</span>

       <span class="kt">double</span> <span class="n">sigma_s_</span><span class="p">;</span>
       <span class="kt">double</span> <span class="n">sigma_r_</span><span class="p">;</span>
       <span class="n">KdTreePtr</span> <span class="n">tree_</span><span class="p">;</span>
   <span class="p">};</span>
 <span class="p">}</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_H_</span>
</pre></div>
</td></tr></table></div>
</div>
<div class="section" id="id27">
<h2><a class="toc-backref" href="#id53">bilateral.hpp</a></h2>
<p>There’re two methods that we need to implement here, namely <cite>applyFilter</cite> and
<cite>computePointWeight</cite>.</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">double</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="kt">double</span> <span class="n">BF</span> <span class="o">=</span> <span class="mi">0</span><span class="p">,</span> <span class="n">W</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>

   <span class="c1">// For each neighbor</span>
   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">n_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">n_id</span> <span class="o">&lt;</span> <span class="n">indices</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">n_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="kt">double</span> <span class="n">id</span> <span class="o">=</span> <span class="n">indices</span><span class="p">[</span><span class="n">n_id</span><span class="p">];</span>
     <span class="kt">double</span> <span class="n">dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">sqrt</span> <span class="p">(</span><span class="n">distances</span><span class="p">[</span><span class="n">n_id</span><span class="p">]);</span>
     <span class="kt">double</span> <span class="n">intensity_dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">abs</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">pid</span><span class="p">].</span><span class="n">intensity</span> <span class="o">-</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">);</span>

     <span class="kt">double</span> <span class="n">weight</span> <span class="o">=</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">dist</span><span class="p">,</span> <span class="n">sigma_s_</span><span class="p">)</span> <span class="o">*</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">intensity_dist</span><span class="p">,</span> <span class="n">sigma_r_</span><span class="p">);</span>

     <span class="n">BF</span> <span class="o">+=</span> <span class="n">weight</span> <span class="o">*</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">;</span>
     <span class="n">W</span> <span class="o">+=</span> <span class="n">weight</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">return</span> <span class="p">(</span><span class="n">BF</span> <span class="o">/</span> <span class="n">W</span><span class="p">);</span>
 <span class="p">}</span>

 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">void</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">input_</span><span class="p">);</span>

   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">k_indices</span><span class="p">;</span>
   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="n">k_distances</span><span class="p">;</span>

   <span class="n">output</span> <span class="o">=</span> <span class="o">*</span><span class="n">input_</span><span class="p">;</span>

   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">point_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">point_id</span> <span class="o">&lt;</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">point_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">radiusSearch</span> <span class="p">(</span><span class="n">point_id</span><span class="p">,</span> <span class="n">sigma_s_</span> <span class="o">*</span> <span class="mi">2</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>

     <span class="n">output</span><span class="p">.</span><span class="n">points</span><span class="p">[</span><span class="n">point_id</span><span class="p">].</span><span class="n">intensity</span> <span class="o">=</span> <span class="n">computePointWeight</span> <span class="p">(</span><span class="n">point_id</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>
   <span class="p">}</span>

 <span class="p">}</span>
</pre></div>
</td></tr></table></div>
<p>The <cite>computePointWeight</cite> method should be straightforward as it’s <em>almost
identical</em> to lines 45-58 from section <a class="reference internal" href="#bilateral-filter-example"><span class="std std-ref">Example: a bilateral filter</span></a>. We
basically pass in a point index that we want to compute the intensity weight
for, and a set of neighboring points with distances.</p>
<p>In <cite>applyFilter</cite>, we first set the input data in the tree, copy all the input
data into the output, and then proceed at computing the new weighted point
intensities.</p>
<p>Looking back at <a class="reference internal" href="#filling"><span class="std std-ref">Filling in the class structure</span></a>, it’s now time to declare the <cite>PCL_INSTANTIATE</cite>
entry for the class:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#ifndef PCL_FILTERS_BILATERAL_IMPL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_IMPL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>

 <span class="p">...</span>

 <span class="cp">#define PCL_INSTANTIATE_BilateralFilter(T) template class PCL_EXPORTS pcl::BilateralFilter&lt;T&gt;;</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_IMPL_H_</span>
</pre></div>
</td></tr></table></div>
<p>One additional thing that we can do is error checking on:</p>
<blockquote>
<div><ul class="simple">
<li><p>whether the two <cite>sigma_s_</cite> and <cite>sigma_r_</cite> parameters have been given;</p></li>
<li><p>whether the search method object (i.e., <cite>tree_</cite>) has been set.</p></li>
</ul>
</div></blockquote>
<p>For the former, we’re going to check the value of <cite>sigma_s_</cite>, which was set to
a default of 0, and has a critical importance for the behavior of the algorithm
(it basically defines the size of the support region). Therefore, if at the
execution of the code, its value is still 0, we will print an error using the
<a href="#id28"><span class="problematic" id="id29">:pcl:`PCL_ERROR&lt;PCL_ERROR&gt;`</span></a> macro, and return.</p>
<p>In the case of the search method, we can either do the same, or be clever and
provide a default option for the user. The best default options are:</p>
<blockquote>
<div><ul class="simple">
<li><p>use an organized search method via <a href="#id30"><span class="problematic" id="id31">:pcl:`pcl::OrganizedNeighbor&lt;pcl::OrganizedNeighbor&gt;`</span></a> if the point cloud is organized;</p></li>
<li><p>use a general purpose kdtree via <a href="#id32"><span class="problematic" id="id33">:pcl:`pcl::KdTreeFLANN&lt;pcl::KdTreeFLANN&gt;`</span></a> if the point cloud is unorganized.</p></li>
</ul>
</div></blockquote>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/organized_data.h&gt;</span><span class="cp"></span>

 <span class="p">...</span>
 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">void</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="k">if</span> <span class="p">(</span><span class="n">sigma_s_</span> <span class="o">==</span> <span class="mi">0</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">PCL_ERROR</span> <span class="p">(</span><span class="s">&quot;[pcl::BilateralFilter::applyFilter] Need a sigma_s value given before continuing.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">);</span>
     <span class="k">return</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">if</span> <span class="p">(</span><span class="o">!</span><span class="n">tree_</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="k">if</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">isOrganized</span> <span class="p">())</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">OrganizedNeighbor</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">());</span>
     <span class="k">else</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">(</span><span class="nb">false</span><span class="p">));</span>
   <span class="p">}</span>
   <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">input_</span><span class="p">);</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
<p>The implementation file header thus becomes:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#ifndef PCL_FILTERS_BILATERAL_IMPL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_IMPL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/organized_data.h&gt;</span><span class="cp"></span>

 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">double</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="kt">double</span> <span class="n">BF</span> <span class="o">=</span> <span class="mi">0</span><span class="p">,</span> <span class="n">W</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>

   <span class="c1">// For each neighbor</span>
   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">n_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">n_id</span> <span class="o">&lt;</span> <span class="n">indices</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">n_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="kt">double</span> <span class="n">id</span> <span class="o">=</span> <span class="n">indices</span><span class="p">[</span><span class="n">n_id</span><span class="p">];</span>
     <span class="kt">double</span> <span class="n">dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">sqrt</span> <span class="p">(</span><span class="n">distances</span><span class="p">[</span><span class="n">n_id</span><span class="p">]);</span>
     <span class="kt">double</span> <span class="n">intensity_dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">abs</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">pid</span><span class="p">].</span><span class="n">intensity</span> <span class="o">-</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">);</span>

     <span class="kt">double</span> <span class="n">weight</span> <span class="o">=</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">dist</span><span class="p">,</span> <span class="n">sigma_s_</span><span class="p">)</span> <span class="o">*</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">intensity_dist</span><span class="p">,</span> <span class="n">sigma_r_</span><span class="p">);</span>

     <span class="n">BF</span> <span class="o">+=</span> <span class="n">weight</span> <span class="o">*</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">;</span>
     <span class="n">W</span> <span class="o">+=</span> <span class="n">weight</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">return</span> <span class="p">(</span><span class="n">BF</span> <span class="o">/</span> <span class="n">W</span><span class="p">);</span>
 <span class="p">}</span>

 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">void</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="k">if</span> <span class="p">(</span><span class="n">sigma_s_</span> <span class="o">==</span> <span class="mi">0</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">PCL_ERROR</span> <span class="p">(</span><span class="s">&quot;[pcl::BilateralFilter::applyFilter] Need a sigma_s value given before continuing.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">);</span>
     <span class="k">return</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">if</span> <span class="p">(</span><span class="o">!</span><span class="n">tree_</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="k">if</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">isOrganized</span> <span class="p">())</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">OrganizedNeighbor</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">());</span>
     <span class="k">else</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">(</span><span class="nb">false</span><span class="p">));</span>
   <span class="p">}</span>
   <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">input_</span><span class="p">);</span>

   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">k_indices</span><span class="p">;</span>
   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="n">k_distances</span><span class="p">;</span>

   <span class="n">output</span> <span class="o">=</span> <span class="o">*</span><span class="n">input_</span><span class="p">;</span>

   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">point_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">point_id</span> <span class="o">&lt;</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">point_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">radiusSearch</span> <span class="p">(</span><span class="n">point_id</span><span class="p">,</span> <span class="n">sigma_s_</span> <span class="o">*</span> <span class="mi">2</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>

     <span class="n">output</span><span class="p">.</span><span class="n">points</span><span class="p">[</span><span class="n">point_id</span><span class="p">].</span><span class="n">intensity</span> <span class="o">=</span> <span class="n">computePointWeight</span> <span class="p">(</span><span class="n">point_id</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>
   <span class="p">}</span>
 <span class="p">}</span>

 <span class="cp">#define PCL_INSTANTIATE_BilateralFilter(T) template class PCL_EXPORTS pcl::BilateralFilter&lt;T&gt;;</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_IMPL_H_</span>
</pre></div>
</td></tr></table></div>
</div>
</div>
<div class="section" id="taking-advantage-of-other-pcl-concepts">
<h1><a class="toc-backref" href="#id54">Taking advantage of other PCL concepts</a></h1>
<div class="section" id="point-indices">
<h2><a class="toc-backref" href="#id55">Point indices</a></h2>
<p>The standard way of passing point cloud data into PCL algorithms is via
<a href="#id34"><span class="problematic" id="id35">:pcl:`setInputCloud&lt;pcl::PCLBase::setInputCloud&gt;`</span></a> calls. In addition, PCL also
defines a way to define a region of interest / <em>list of point indices</em> that the
algorithm should operate on, rather than the entire cloud, via
<a href="#id36"><span class="problematic" id="id37">:pcl:`setIndices&lt;pcl::PCLBase::setIndices&gt;`</span></a>.</p>
<p>All classes inheriting from <a href="#id38"><span class="problematic" id="id39">:pcl:`PCLBase&lt;pcl::PCLBase&gt;`</span></a> exhbit the following
behavior: in case no set of indices is given by the user, a fake one is created
once and used for the duration of the algorithm. This means that we could
easily change the implementation code above to operate on a <em>&lt;cloud, indices&gt;</em>
tuple, which has the added advantage that if the user does pass a set of
indices, only those will be used, and if not, the entire cloud will be used.</p>
<p>The new <em>bilateral.hpp</em> class thus becomes:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/organized_data.h&gt;</span><span class="cp"></span>

 <span class="p">...</span>
 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">void</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="k">if</span> <span class="p">(</span><span class="n">sigma_s_</span> <span class="o">==</span> <span class="mi">0</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">PCL_ERROR</span> <span class="p">(</span><span class="s">&quot;[pcl::BilateralFilter::applyFilter] Need a sigma_s value given before continuing.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">);</span>
     <span class="k">return</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">if</span> <span class="p">(</span><span class="o">!</span><span class="n">tree_</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="k">if</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">isOrganized</span> <span class="p">())</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">OrganizedNeighbor</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">());</span>
     <span class="k">else</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">(</span><span class="nb">false</span><span class="p">));</span>
   <span class="p">}</span>
   <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">input_</span><span class="p">);</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
<p>The implementation file header thus becomes:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#ifndef PCL_FILTERS_BILATERAL_IMPL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_IMPL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/organized_data.h&gt;</span><span class="cp"></span>

 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">double</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="kt">double</span> <span class="n">BF</span> <span class="o">=</span> <span class="mi">0</span><span class="p">,</span> <span class="n">W</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>

   <span class="c1">// For each neighbor</span>
   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">n_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">n_id</span> <span class="o">&lt;</span> <span class="n">indices</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">n_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="kt">double</span> <span class="n">id</span> <span class="o">=</span> <span class="n">indices</span><span class="p">[</span><span class="n">n_id</span><span class="p">];</span>
     <span class="kt">double</span> <span class="n">dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">sqrt</span> <span class="p">(</span><span class="n">distances</span><span class="p">[</span><span class="n">n_id</span><span class="p">]);</span>
     <span class="kt">double</span> <span class="n">intensity_dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">abs</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">pid</span><span class="p">].</span><span class="n">intensity</span> <span class="o">-</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">);</span>

     <span class="kt">double</span> <span class="n">weight</span> <span class="o">=</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">dist</span><span class="p">,</span> <span class="n">sigma_s_</span><span class="p">)</span> <span class="o">*</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">intensity_dist</span><span class="p">,</span> <span class="n">sigma_r_</span><span class="p">);</span>

     <span class="n">BF</span> <span class="o">+=</span> <span class="n">weight</span> <span class="o">*</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">;</span>
     <span class="n">W</span> <span class="o">+=</span> <span class="n">weight</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">return</span> <span class="p">(</span><span class="n">BF</span> <span class="o">/</span> <span class="n">W</span><span class="p">);</span>
 <span class="p">}</span>

 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">void</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="k">if</span> <span class="p">(</span><span class="n">sigma_s_</span> <span class="o">==</span> <span class="mi">0</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">PCL_ERROR</span> <span class="p">(</span><span class="s">&quot;[pcl::BilateralFilter::applyFilter] Need a sigma_s value given before continuing.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">);</span>
     <span class="k">return</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">if</span> <span class="p">(</span><span class="o">!</span><span class="n">tree_</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="k">if</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">isOrganized</span> <span class="p">())</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">OrganizedNeighbor</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">());</span>
     <span class="k">else</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">(</span><span class="nb">false</span><span class="p">));</span>
   <span class="p">}</span>
   <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">input_</span><span class="p">);</span>

   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">k_indices</span><span class="p">;</span>
   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="n">k_distances</span><span class="p">;</span>

   <span class="n">output</span> <span class="o">=</span> <span class="o">*</span><span class="n">input_</span><span class="p">;</span>

   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="n">indices_</span><span class="o">-&gt;</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">i</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">radiusSearch</span> <span class="p">((</span><span class="o">*</span><span class="n">indices_</span><span class="p">)[</span><span class="n">i</span><span class="p">],</span> <span class="n">sigma_s_</span> <span class="o">*</span> <span class="mi">2</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>

     <span class="n">output</span><span class="p">.</span><span class="n">points</span><span class="p">[(</span><span class="o">*</span><span class="n">indices_</span><span class="p">)[</span><span class="n">i</span><span class="p">]].</span><span class="n">intensity</span> <span class="o">=</span> <span class="n">computePointWeight</span> <span class="p">((</span><span class="o">*</span><span class="n">indices_</span><span class="p">)[</span><span class="n">i</span><span class="p">],</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>
   <span class="p">}</span>
 <span class="p">}</span>

 <span class="cp">#define PCL_INSTANTIATE_BilateralFilter(T) template class PCL_EXPORTS pcl::BilateralFilter&lt;T&gt;;</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_IMPL_H_</span>
</pre></div>
</td></tr></table></div>
<p>To make <a href="#id40"><span class="problematic" id="id41">:pcl:`indices_&lt;pcl::PCLBase::indices_&gt;`</span></a> work without typing the full
construct, we need to add a new line to <em>bilateral.h</em> that specifies the class
where <cite>indices_</cite> is declared:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1
2
3
4
5
6
7
8
9</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="p">...</span>
   <span class="k">template</span><span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span>
   <span class="k">class</span> <span class="nc">BilateralFilter</span> <span class="o">:</span> <span class="k">public</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span>
   <span class="p">{</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">input_</span><span class="p">;</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">indices_</span><span class="p">;</span>
     <span class="k">public</span><span class="o">:</span>
       <span class="n">BilateralFilter</span> <span class="p">()</span> <span class="o">:</span> <span class="n">sigma_s_</span> <span class="p">(</span><span class="mi">0</span><span class="p">),</span>
 <span class="p">...</span>
</pre></div>
</td></tr></table></div>
</div>
<div class="section" id="licenses">
<h2><a class="toc-backref" href="#id56">Licenses</a></h2>
<p>It is advised that each file contains a license that describes the author of
the code. This is very useful for our users that need to understand what sort
of restrictions are they bound to when using the code. PCL is 100% <strong>BSD
licensed</strong>, and we insert the corpus of the license as a C++ comment in the
file, as follows:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cm">/*</span>
<span class="cm">  * Software License Agreement (BSD License)</span>
<span class="cm">  *</span>
<span class="cm">  *  Point Cloud Library (PCL) - www.pointclouds.org</span>
<span class="cm">  *  Copyright (c) 2010-2011, Willow Garage, Inc.</span>
<span class="cm">  *</span>
<span class="cm">  *  All rights reserved.</span>
<span class="cm">  *</span>
<span class="cm">  *  Redistribution and use in source and binary forms, with or without</span>
<span class="cm">  *  modification, are permitted provided that the following conditions</span>
<span class="cm">  *  are met:</span>
<span class="cm">  *</span>
<span class="cm">  *   * Redistributions of source code must retain the above copyright</span>
<span class="cm">  *     notice, this list of conditions and the following disclaimer.</span>
<span class="cm">  *   * Redistributions in binary form must reproduce the above</span>
<span class="cm">  *     copyright notice, this list of conditions and the following</span>
<span class="cm">  *     disclaimer in the documentation and/or other materials provided</span>
<span class="cm">  *     with the distribution.</span>
<span class="cm">  *   * Neither the name of Willow Garage, Inc. nor the names of its</span>
<span class="cm">  *     contributors may be used to endorse or promote products derived</span>
<span class="cm">  *     from this software without specific prior written permission.</span>
<span class="cm">  *</span>
<span class="cm">  *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS</span>
<span class="cm">  *  &quot;AS IS&quot; AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT</span>
<span class="cm">  *  LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS</span>
<span class="cm">  *  FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE</span>
<span class="cm">  *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,</span>
<span class="cm">  *  INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,</span>
<span class="cm">  *  BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;</span>
<span class="cm">  *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER</span>
<span class="cm">  *  CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT</span>
<span class="cm">  *  LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN</span>
<span class="cm">  *  ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE</span>
<span class="cm">  *  POSSIBILITY OF SUCH DAMAGE.</span>
<span class="cm">  *</span>
<span class="cm">  */</span>
</pre></div>
</td></tr></table></div>
<p>An additional like can be inserted if additional copyright is needed (or the
original copyright can be changed):</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>1</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="o">*</span> <span class="n">Copyright</span> <span class="p">(</span><span class="n">c</span><span class="p">)</span> <span class="n">XXX</span><span class="p">,</span> <span class="n">respective</span> <span class="n">authors</span><span class="p">.</span>
</pre></div>
</td></tr></table></div>
</div>
<div class="section" id="proper-naming">
<h2><a class="toc-backref" href="#id57">Proper naming</a></h2>
<p>We wrote the tutorial so far by using <em>silly named</em> setters and getters in our
example, like <cite>setSigmaS</cite> or <cite>setSigmaR</cite>. In reality, we would like to use a
better naming scheme, that actually represents what the parameter is doing. In
a final version of the code we could therefore rename the setters and getters
to <cite>set/getHalfSize</cite> and <cite>set/getStdDev</cite> or something similar.</p>
</div>
<div class="section" id="code-comments">
<h2><a class="toc-backref" href="#id58">Code comments</a></h2>
<p>PCL is trying to maintain a <em>high standard</em> with respect to user and API
documentation. This sort of Doxygen documentation has been stripped from the
examples shown above. In reality, we would have had the <em>bilateral.h</em> header
class look like:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>  1
  2
  3
  4
  5
  6
  7
  8
  9
 10
 11
 12
 13
 14
 15
 16
 17
 18
 19
 20
 21
 22
 23
 24
 25
 26
 27
 28
 29
 30
 31
 32
 33
 34
 35
 36
 37
 38
 39
 40
 41
 42
 43
 44
 45
 46
 47
 48
 49
 50
 51
 52
 53
 54
 55
 56
 57
 58
 59
 60
 61
 62
 63
 64
 65
 66
 67
 68
 69
 70
 71
 72
 73
 74
 75
 76
 77
 78
 79
 80
 81
 82
 83
 84
 85
 86
 87
 88
 89
 90
 91
 92
 93
 94
 95
 96
 97
 98
 99
100
101
102
103
104
105
106
107
108
109
110
111
112
113
114
115
116
117
118
119
120
121
122
123
124
125
126
127
128
129
130
131
132
133
134
135
136
137
138
139
140
141
142
143
144
145
146
147
148
149</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cm">/*</span>
<span class="cm">  * Software License Agreement (BSD License)</span>
<span class="cm">  *</span>
<span class="cm">  *  Point Cloud Library (PCL) - www.pointclouds.org</span>
<span class="cm">  *  Copyright (c) 2010-2011, Willow Garage, Inc.</span>
<span class="cm">  *</span>
<span class="cm">  *  All rights reserved.</span>
<span class="cm">  *</span>
<span class="cm">  *  Redistribution and use in source and binary forms, with or without</span>
<span class="cm">  *  modification, are permitted provided that the following conditions</span>
<span class="cm">  *  are met:</span>
<span class="cm">  *</span>
<span class="cm">  *   * Redistributions of source code must retain the above copyright</span>
<span class="cm">  *     notice, this list of conditions and the following disclaimer.</span>
<span class="cm">  *   * Redistributions in binary form must reproduce the above</span>
<span class="cm">  *     copyright notice, this list of conditions and the following</span>
<span class="cm">  *     disclaimer in the documentation and/or other materials provided</span>
<span class="cm">  *     with the distribution.</span>
<span class="cm">  *   * Neither the name of Willow Garage, Inc. nor the names of its</span>
<span class="cm">  *     contributors may be used to endorse or promote products derived</span>
<span class="cm">  *     from this software without specific prior written permission.</span>
<span class="cm">  *</span>
<span class="cm">  *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS</span>
<span class="cm">  *  &quot;AS IS&quot; AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT</span>
<span class="cm">  *  LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS</span>
<span class="cm">  *  FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE</span>
<span class="cm">  *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,</span>
<span class="cm">  *  INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,</span>
<span class="cm">  *  BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;</span>
<span class="cm">  *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER</span>
<span class="cm">  *  CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT</span>
<span class="cm">  *  LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN</span>
<span class="cm">  *  ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE</span>
<span class="cm">  *  POSSIBILITY OF SUCH DAMAGE.</span>
<span class="cm">  *</span>
<span class="cm">  */</span>

 <span class="cp">#ifndef PCL_FILTERS_BILATERAL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/filter.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree.h&gt;</span><span class="cp"></span>

 <span class="k">namespace</span> <span class="n">pcl</span>
 <span class="p">{</span>
   <span class="cm">/** \brief A bilateral filter implementation for point cloud data. Uses the intensity data channel.</span>
<span class="cm">     * \note For more information please see</span>
<span class="cm">     * &lt;b&gt;C. Tomasi and R. Manduchi. Bilateral Filtering for Gray and Color Images.</span>
<span class="cm">     * In Proceedings of the IEEE International Conference on Computer Vision,</span>
<span class="cm">     * 1998.&lt;/b&gt;</span>
<span class="cm">     * \author Luca Penasa</span>
<span class="cm">     */</span>
   <span class="k">template</span><span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span>
   <span class="k">class</span> <span class="nc">BilateralFilter</span> <span class="o">:</span> <span class="k">public</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span>
   <span class="p">{</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">input_</span><span class="p">;</span>
     <span class="k">using</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">indices_</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">Filter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">PointCloud</span> <span class="n">PointCloud</span><span class="p">;</span>
     <span class="k">typedef</span> <span class="k">typename</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTree</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">KdTreePtr</span><span class="p">;</span>

     <span class="k">public</span><span class="o">:</span>
       <span class="cm">/** \brief Constructor.</span>
<span class="cm">         * Sets \ref sigma_s_ to 0 and \ref sigma_r_ to MAXDBL</span>
<span class="cm">         */</span>
       <span class="n">BilateralFilter</span> <span class="p">()</span> <span class="o">:</span> <span class="n">sigma_s_</span> <span class="p">(</span><span class="mi">0</span><span class="p">),</span>
                            <span class="n">sigma_r_</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="n">numeric_limits</span><span class="o">&lt;</span><span class="kt">double</span><span class="o">&gt;::</span><span class="n">max</span> <span class="p">())</span>
       <span class="p">{</span>
       <span class="p">}</span>


       <span class="cm">/** \brief Filter the input data and store the results into output</span>
<span class="cm">         * \param[out] output the resultant point cloud message</span>
<span class="cm">         */</span>
       <span class="kt">void</span>
       <span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">);</span>

       <span class="cm">/** \brief Compute the intensity average for a single point</span>
<span class="cm">         * \param[in] pid the point index to compute the weight for</span>
<span class="cm">         * \param[in] indices the set of nearest neighor indices</span>
<span class="cm">         * \param[in] distances the set of nearest neighbor distances</span>
<span class="cm">         * \return the intensity average at a given point index</span>
<span class="cm">         */</span>
       <span class="kt">double</span>
       <span class="nf">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span> <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span> <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">);</span>

       <span class="cm">/** \brief Set the half size of the Gaussian bilateral filter window.</span>
<span class="cm">         * \param[in] sigma_s the half size of the Gaussian bilateral filter window to use</span>
<span class="cm">         */</span>
       <span class="kr">inline</span> <span class="kt">void</span>
       <span class="nf">setHalfSize</span> <span class="p">(</span><span class="k">const</span> <span class="kt">double</span> <span class="n">sigma_s</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">sigma_s_</span> <span class="o">=</span> <span class="n">sigma_s</span><span class="p">;</span>
       <span class="p">}</span>

       <span class="cm">/** \brief Get the half size of the Gaussian bilateral filter window as set by the user. */</span>
       <span class="kt">double</span>
       <span class="nf">getHalfSize</span> <span class="p">()</span> <span class="k">const</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">sigma_s_</span><span class="p">);</span>
       <span class="p">}</span>

       <span class="cm">/** \brief Set the standard deviation parameter</span>
<span class="cm">         * \param[in] sigma_r the new standard deviation parameter</span>
<span class="cm">         */</span>
       <span class="kt">void</span>
       <span class="nf">setStdDev</span> <span class="p">(</span><span class="k">const</span> <span class="kt">double</span> <span class="n">sigma_r</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">sigma_r_</span> <span class="o">=</span> <span class="n">sigma_r</span><span class="p">;</span>
       <span class="p">}</span>

       <span class="cm">/** \brief Get the value of the current standard deviation parameter of the bilateral filter. */</span>
       <span class="kt">double</span>
       <span class="nf">getStdDev</span> <span class="p">()</span> <span class="k">const</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">sigma_r_</span><span class="p">);</span>
       <span class="p">}</span>

       <span class="cm">/** \brief Provide a pointer to the search object.</span>
<span class="cm">         * \param[in] tree a pointer to the spatial search object.</span>
<span class="cm">         */</span>
       <span class="kt">void</span>
       <span class="nf">setSearchMethod</span> <span class="p">(</span><span class="k">const</span> <span class="n">KdTreePtr</span> <span class="o">&amp;</span><span class="n">tree</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="n">tree_</span> <span class="o">=</span> <span class="n">tree</span><span class="p">;</span>
       <span class="p">}</span>

     <span class="k">private</span><span class="o">:</span>

       <span class="cm">/** \brief The bilateral filter Gaussian distance kernel.</span>
<span class="cm">         * \param[in] x the spatial distance (distance or intensity)</span>
<span class="cm">         * \param[in] sigma standard deviation</span>
<span class="cm">         */</span>
       <span class="kr">inline</span> <span class="kt">double</span>
       <span class="n">kernel</span> <span class="p">(</span><span class="kt">double</span> <span class="n">x</span><span class="p">,</span> <span class="kt">double</span> <span class="n">sigma</span><span class="p">)</span>
       <span class="p">{</span>
         <span class="k">return</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="n">exp</span> <span class="p">(</span><span class="o">-</span> <span class="p">(</span><span class="n">x</span><span class="o">*</span><span class="n">x</span><span class="p">)</span><span class="o">/</span><span class="p">(</span><span class="mi">2</span><span class="o">*</span><span class="n">sigma</span><span class="o">*</span><span class="n">sigma</span><span class="p">)));</span>
       <span class="p">}</span>

       <span class="cm">/** \brief The half size of the Gaussian bilateral filter window (e.g., spatial extents in Euclidean). */</span>
       <span class="kt">double</span> <span class="n">sigma_s_</span><span class="p">;</span>
       <span class="cm">/** \brief The standard deviation of the bilateral filter (e.g., standard deviation in intensity). */</span>
       <span class="kt">double</span> <span class="n">sigma_r_</span><span class="p">;</span>

       <span class="cm">/** \brief A pointer to the spatial search object. */</span>
       <span class="n">KdTreePtr</span> <span class="n">tree_</span><span class="p">;</span>
   <span class="p">};</span>
 <span class="p">}</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_H_</span>
</pre></div>
</td></tr></table></div>
<p>And the <em>bilateral.hpp</em> likes:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre>  1
  2
  3
  4
  5
  6
  7
  8
  9
 10
 11
 12
 13
 14
 15
 16
 17
 18
 19
 20
 21
 22
 23
 24
 25
 26
 27
 28
 29
 30
 31
 32
 33
 34
 35
 36
 37
 38
 39
 40
 41
 42
 43
 44
 45
 46
 47
 48
 49
 50
 51
 52
 53
 54
 55
 56
 57
 58
 59
 60
 61
 62
 63
 64
 65
 66
 67
 68
 69
 70
 71
 72
 73
 74
 75
 76
 77
 78
 79
 80
 81
 82
 83
 84
 85
 86
 87
 88
 89
 90
 91
 92
 93
 94
 95
 96
 97
 98
 99
100
101
102
103
104
105
106
107
108
109
110
111
112</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cm">/*</span>
<span class="cm">  * Software License Agreement (BSD License)</span>
<span class="cm">  *</span>
<span class="cm">  *  Point Cloud Library (PCL) - www.pointclouds.org</span>
<span class="cm">  *  Copyright (c) 2010-2011, Willow Garage, Inc.</span>
<span class="cm">  *</span>
<span class="cm">  *  All rights reserved.</span>
<span class="cm">  *</span>
<span class="cm">  *  Redistribution and use in source and binary forms, with or without</span>
<span class="cm">  *  modification, are permitted provided that the following conditions</span>
<span class="cm">  *  are met:</span>
<span class="cm">  *</span>
<span class="cm">  *   * Redistributions of source code must retain the above copyright</span>
<span class="cm">  *     notice, this list of conditions and the following disclaimer.</span>
<span class="cm">  *   * Redistributions in binary form must reproduce the above</span>
<span class="cm">  *     copyright notice, this list of conditions and the following</span>
<span class="cm">  *     disclaimer in the documentation and/or other materials provided</span>
<span class="cm">  *     with the distribution.</span>
<span class="cm">  *   * Neither the name of Willow Garage, Inc. nor the names of its</span>
<span class="cm">  *     contributors may be used to endorse or promote products derived</span>
<span class="cm">  *     from this software without specific prior written permission.</span>
<span class="cm">  *</span>
<span class="cm">  *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS</span>
<span class="cm">  *  &quot;AS IS&quot; AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT</span>
<span class="cm">  *  LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS</span>
<span class="cm">  *  FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE</span>
<span class="cm">  *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,</span>
<span class="cm">  *  INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,</span>
<span class="cm">  *  BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;</span>
<span class="cm">  *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER</span>
<span class="cm">  *  CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT</span>
<span class="cm">  *  LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN</span>
<span class="cm">  *  ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE</span>
<span class="cm">  *  POSSIBILITY OF SUCH DAMAGE.</span>
<span class="cm">  *</span>
<span class="cm">  */</span>

 <span class="cp">#ifndef PCL_FILTERS_BILATERAL_IMPL_H_</span>
 <span class="cp">#define PCL_FILTERS_BILATERAL_IMPL_H_</span>

 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/organized_data.h&gt;</span><span class="cp"></span>

 <span class="c1">//////////////////////////////////////////////////////////////////////////////////////////////</span>
 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">double</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">computePointWeight</span> <span class="p">(</span><span class="k">const</span> <span class="kt">int</span> <span class="n">pid</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">indices</span><span class="p">,</span>
                                                   <span class="k">const</span> <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="o">&amp;</span><span class="n">distances</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="kt">double</span> <span class="n">BF</span> <span class="o">=</span> <span class="mi">0</span><span class="p">,</span> <span class="n">W</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>

   <span class="c1">// For each neighbor</span>
   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">n_id</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">n_id</span> <span class="o">&lt;</span> <span class="n">indices</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">n_id</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="kt">double</span> <span class="n">id</span> <span class="o">=</span> <span class="n">indices</span><span class="p">[</span><span class="n">n_id</span><span class="p">];</span>
     <span class="c1">// Compute the difference in intensity</span>
     <span class="kt">double</span> <span class="n">intensity_dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">abs</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">pid</span><span class="p">].</span><span class="n">intensity</span> <span class="o">-</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">);</span>

     <span class="c1">// Compute the Gaussian intensity weights both in Euclidean and in intensity space</span>
     <span class="kt">double</span> <span class="n">dist</span> <span class="o">=</span> <span class="n">std</span><span class="o">::</span><span class="n">sqrt</span> <span class="p">(</span><span class="n">distances</span><span class="p">[</span><span class="n">n_id</span><span class="p">]);</span>
     <span class="kt">double</span> <span class="n">weight</span> <span class="o">=</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">dist</span><span class="p">,</span> <span class="n">sigma_s_</span><span class="p">)</span> <span class="o">*</span> <span class="n">kernel</span> <span class="p">(</span><span class="n">intensity_dist</span><span class="p">,</span> <span class="n">sigma_r_</span><span class="p">);</span>

     <span class="c1">// Calculate the bilateral filter response</span>
     <span class="n">BF</span> <span class="o">+=</span> <span class="n">weight</span> <span class="o">*</span> <span class="n">input_</span><span class="o">-&gt;</span><span class="n">points</span><span class="p">[</span><span class="n">id</span><span class="p">].</span><span class="n">intensity</span><span class="p">;</span>
     <span class="n">W</span> <span class="o">+=</span> <span class="n">weight</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="k">return</span> <span class="p">(</span><span class="n">BF</span> <span class="o">/</span> <span class="n">W</span><span class="p">);</span>
 <span class="p">}</span>

 <span class="c1">//////////////////////////////////////////////////////////////////////////////////////////////</span>
 <span class="k">template</span> <span class="o">&lt;</span><span class="k">typename</span> <span class="n">PointT</span><span class="o">&gt;</span> <span class="kt">void</span>
 <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">applyFilter</span> <span class="p">(</span><span class="n">PointCloud</span> <span class="o">&amp;</span><span class="n">output</span><span class="p">)</span>
 <span class="p">{</span>
   <span class="c1">// Check if sigma_s has been given by the user</span>
   <span class="k">if</span> <span class="p">(</span><span class="n">sigma_s_</span> <span class="o">==</span> <span class="mi">0</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="n">PCL_ERROR</span> <span class="p">(</span><span class="s">&quot;[pcl::BilateralFilter::applyFilter] Need a sigma_s value given before continuing.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">);</span>
     <span class="k">return</span><span class="p">;</span>
   <span class="p">}</span>
   <span class="c1">// In case a search method has not been given, initialize it using some defaults</span>
   <span class="k">if</span> <span class="p">(</span><span class="o">!</span><span class="n">tree_</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="c1">// For organized datasets, use an OrganizedNeighbor</span>
     <span class="k">if</span> <span class="p">(</span><span class="n">input_</span><span class="o">-&gt;</span><span class="n">isOrganized</span> <span class="p">())</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">OrganizedNeighbor</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">());</span>
     <span class="c1">// For unorganized data, use a FLANN kdtree</span>
     <span class="k">else</span>
       <span class="n">tree_</span><span class="p">.</span><span class="n">reset</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="p">(</span><span class="nb">false</span><span class="p">));</span>
   <span class="p">}</span>
   <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">input_</span><span class="p">);</span>

   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">k_indices</span><span class="p">;</span>
   <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">float</span><span class="o">&gt;</span> <span class="n">k_distances</span><span class="p">;</span>

   <span class="c1">// Copy the input data into the output</span>
   <span class="n">output</span> <span class="o">=</span> <span class="o">*</span><span class="n">input_</span><span class="p">;</span>

   <span class="c1">// For all the indices given (equal to the entire cloud if none given)</span>
   <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="n">indices_</span><span class="o">-&gt;</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">i</span><span class="p">)</span>
   <span class="p">{</span>
     <span class="c1">// Perform a radius search to find the nearest neighbors</span>
     <span class="n">tree_</span><span class="o">-&gt;</span><span class="n">radiusSearch</span> <span class="p">((</span><span class="o">*</span><span class="n">indices_</span><span class="p">)[</span><span class="n">i</span><span class="p">],</span> <span class="n">sigma_s_</span> <span class="o">*</span> <span class="mi">2</span><span class="p">,</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>

     <span class="c1">// Overwrite the intensity value with the computed average</span>
     <span class="n">output</span><span class="p">.</span><span class="n">points</span><span class="p">[(</span><span class="o">*</span><span class="n">indices_</span><span class="p">)[</span><span class="n">i</span><span class="p">]].</span><span class="n">intensity</span> <span class="o">=</span> <span class="n">computePointWeight</span> <span class="p">((</span><span class="o">*</span><span class="n">indices_</span><span class="p">)[</span><span class="n">i</span><span class="p">],</span> <span class="n">k_indices</span><span class="p">,</span> <span class="n">k_distances</span><span class="p">);</span>
   <span class="p">}</span>
 <span class="p">}</span>

 <span class="cp">#define PCL_INSTANTIATE_BilateralFilter(T) template class PCL_EXPORTS pcl::BilateralFilter&lt;T&gt;;</span>

 <span class="cp">#endif </span><span class="c1">// PCL_FILTERS_BILATERAL_IMPL_H_</span>
</pre></div>
</td></tr></table></div>
</div>
</div>
<div class="section" id="testing-the-new-class">
<h1><a class="toc-backref" href="#id59">Testing the new class</a></h1>
<p>Testing the new class is easy. We’ll take the first code snippet example as
shown above, strip the algorithm, and make it use the <cite>pcl::BilateralFilter</cite>
class instead:</p>
<div class="highlight-cpp notranslate"><table class="highlighttable"><tr><td class="linenos"><div class="linenodiv"><pre> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35</pre></div></td><td class="code"><div class="highlight"><pre><span></span> <span class="cp">#include</span> <span class="cpf">&lt;pcl/point_types.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/io/pcd_io.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/kdtree/kdtree_flann.h&gt;</span><span class="cp"></span>
 <span class="cp">#include</span> <span class="cpf">&lt;pcl/filters/bilateral.h&gt;</span><span class="cp"></span>

 <span class="k">typedef</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZI</span> <span class="n">PointT</span><span class="p">;</span>

 <span class="kt">int</span>
 <span class="nf">main</span> <span class="p">(</span><span class="kt">int</span> <span class="n">argc</span><span class="p">,</span> <span class="kt">char</span> <span class="o">*</span><span class="n">argv</span><span class="p">[])</span>
 <span class="p">{</span>
   <span class="n">std</span><span class="o">::</span><span class="n">string</span> <span class="n">incloudfile</span> <span class="o">=</span> <span class="n">argv</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
   <span class="n">std</span><span class="o">::</span><span class="n">string</span> <span class="n">outcloudfile</span> <span class="o">=</span> <span class="n">argv</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
   <span class="kt">float</span> <span class="n">sigma_s</span> <span class="o">=</span> <span class="n">atof</span> <span class="p">(</span><span class="n">argv</span><span class="p">[</span><span class="mi">3</span><span class="p">]);</span>
   <span class="kt">float</span> <span class="n">sigma_r</span> <span class="o">=</span> <span class="n">atof</span> <span class="p">(</span><span class="n">argv</span><span class="p">[</span><span class="mi">4</span><span class="p">]);</span>

   <span class="c1">// Load cloud</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">cloud</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span><span class="p">);</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">io</span><span class="o">::</span><span class="n">loadPCDFile</span> <span class="p">(</span><span class="n">incloudfile</span><span class="p">.</span><span class="n">c_str</span> <span class="p">(),</span> <span class="o">*</span><span class="n">cloud</span><span class="p">);</span>

   <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="n">outcloud</span><span class="p">;</span>

   <span class="c1">// Set up KDTree</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">tree</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">KdTreeFLANN</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span><span class="p">);</span>

   <span class="n">pcl</span><span class="o">::</span><span class="n">BilateralFilter</span><span class="o">&lt;</span><span class="n">PointT</span><span class="o">&gt;</span> <span class="n">bf</span><span class="p">;</span>
   <span class="n">bf</span><span class="p">.</span><span class="n">setInputCloud</span> <span class="p">(</span><span class="n">cloud</span><span class="p">);</span>
   <span class="n">bf</span><span class="p">.</span><span class="n">setSearchMethod</span> <span class="p">(</span><span class="n">tree</span><span class="p">);</span>
   <span class="n">bf</span><span class="p">.</span><span class="n">setHalfSize</span> <span class="p">(</span><span class="n">sigma_s</span><span class="p">);</span>
   <span class="n">bf</span><span class="p">.</span><span class="n">setStdDev</span> <span class="p">(</span><span class="n">sigma_r</span><span class="p">);</span>
   <span class="n">bf</span><span class="p">.</span><span class="n">filter</span> <span class="p">(</span><span class="n">outcloud</span><span class="p">);</span>

   <span class="c1">// Save filtered output</span>
   <span class="n">pcl</span><span class="o">::</span><span class="n">io</span><span class="o">::</span><span class="n">savePCDFile</span> <span class="p">(</span><span class="n">outcloudfile</span><span class="p">.</span><span class="n">c_str</span> <span class="p">(),</span> <span class="n">outcloud</span><span class="p">);</span>
   <span class="k">return</span> <span class="p">(</span><span class="mi">0</span><span class="p">);</span>
 <span class="p">}</span>
</pre></div>
</td></tr></table></div>
</div>


          </div>
      </div>
      <div class="clearer"></div>
    </div>
</div> <!-- #page-content -->

<?php
$chunkOutput = $modx->getChunk("site-footer");
echo $chunkOutput;
?>

  </body>
</html>