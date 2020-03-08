<!DOCTYPE html>
<html lang="en">
<head>
<title>Documentation - Point Cloud Library (PCL)</title>
</head>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <title>How to extract NARF keypoint from a range image &#8212; PCL 0.0 documentation</title>
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
            
  <div class="section" id="how-to-extract-narf-keypoint-from-a-range-image">
<span id="narf-keypoint-extraction"></span><h1>How to extract NARF keypoint from a range image</h1>
<p>This tutorial demonstrates how to extract NARF key points from a range image.
The executable enables us to load a point cloud from disc (or create it if not
given), extract interest points on it and visualize the result, both in an
image and a 3D viewer.</p>
</div>
<div class="section" id="the-code">
<h1>The code</h1>
<p>First, create a file called, let’s say, <code class="docutils literal notranslate"><span class="pre">narf_keypoint_extraction.cpp</span></code> in your favorite
editor, and place the following code inside it:</p>
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
149
150
151
152
153
154
155
156
157
158
159
160
161
162
163
164
165
166
167
168
169
170
171
172
173
174
175
176
177
178
179
180
181
182
183
184
185
186
187
188
189
190
191
192
193
194
195
196
197
198
199
200</pre></div></td><td class="code"><div class="highlight"><pre><span></span><span class="cm">/* \author Bastian Steder */</span>

<span class="cp">#include</span> <span class="cpf">&lt;iostream&gt;</span><span class="cp"></span>

<span class="cp">#include</span> <span class="cpf">&lt;pcl/range_image/range_image.h&gt;</span><span class="cp"></span>
<span class="cp">#include</span> <span class="cpf">&lt;pcl/io/pcd_io.h&gt;</span><span class="cp"></span>
<span class="cp">#include</span> <span class="cpf">&lt;pcl/visualization/range_image_visualizer.h&gt;</span><span class="cp"></span>
<span class="cp">#include</span> <span class="cpf">&lt;pcl/visualization/pcl_visualizer.h&gt;</span><span class="cp"></span>
<span class="cp">#include</span> <span class="cpf">&lt;pcl/features/range_image_border_extractor.h&gt;</span><span class="cp"></span>
<span class="cp">#include</span> <span class="cpf">&lt;pcl/keypoints/narf_keypoint.h&gt;</span><span class="cp"></span>
<span class="cp">#include</span> <span class="cpf">&lt;pcl/console/parse.h&gt;</span><span class="cp"></span>

<span class="k">typedef</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span> <span class="n">PointType</span><span class="p">;</span>

<span class="c1">// --------------------</span>
<span class="c1">// -----Parameters-----</span>
<span class="c1">// --------------------</span>
<span class="kt">float</span> <span class="n">angular_resolution</span> <span class="o">=</span> <span class="mf">0.5f</span><span class="p">;</span>
<span class="kt">float</span> <span class="n">support_size</span> <span class="o">=</span> <span class="mf">0.2f</span><span class="p">;</span>
<span class="n">pcl</span><span class="o">::</span><span class="n">RangeImage</span><span class="o">::</span><span class="n">CoordinateFrame</span> <span class="n">coordinate_frame</span> <span class="o">=</span> <span class="n">pcl</span><span class="o">::</span><span class="n">RangeImage</span><span class="o">::</span><span class="n">CAMERA_FRAME</span><span class="p">;</span>
<span class="kt">bool</span> <span class="n">setUnseenToMaxRange</span> <span class="o">=</span> <span class="nb">false</span><span class="p">;</span>

<span class="c1">// --------------</span>
<span class="c1">// -----Help-----</span>
<span class="c1">// --------------</span>
<span class="kt">void</span> 
<span class="nf">printUsage</span> <span class="p">(</span><span class="k">const</span> <span class="kt">char</span><span class="o">*</span> <span class="n">progName</span><span class="p">)</span>
<span class="p">{</span>
  <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;</span><span class="se">\n\n</span><span class="s">Usage: &quot;</span><span class="o">&lt;&lt;</span><span class="n">progName</span><span class="o">&lt;&lt;</span><span class="s">&quot; [options] &lt;scene.pcd&gt;</span><span class="se">\n\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;Options:</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;-------------------------------------------</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;-r &lt;float&gt;   angular resolution in degrees (default &quot;</span><span class="o">&lt;&lt;</span><span class="n">angular_resolution</span><span class="o">&lt;&lt;</span><span class="s">&quot;)</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;-c &lt;int&gt;     coordinate frame (default &quot;</span><span class="o">&lt;&lt;</span> <span class="p">(</span><span class="kt">int</span><span class="p">)</span><span class="n">coordinate_frame</span><span class="o">&lt;&lt;</span><span class="s">&quot;)</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;-m           Treat all unseen points as maximum range readings</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;-s &lt;float&gt;   support size for the interest points (diameter of the used sphere - &quot;</span>
            <span class="o">&lt;&lt;</span>                                                     <span class="s">&quot;default &quot;</span><span class="o">&lt;&lt;</span><span class="n">support_size</span><span class="o">&lt;&lt;</span><span class="s">&quot;)</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;-h           this help</span><span class="se">\n</span><span class="s">&quot;</span>
            <span class="o">&lt;&lt;</span> <span class="s">&quot;</span><span class="se">\n\n</span><span class="s">&quot;</span><span class="p">;</span>
<span class="p">}</span>

<span class="c1">//void </span>
<span class="c1">//setViewerPose (pcl::visualization::PCLVisualizer&amp; viewer, const Eigen::Affine3f&amp; viewer_pose)</span>
<span class="c1">//{</span>
  <span class="c1">//Eigen::Vector3f pos_vector = viewer_pose * Eigen::Vector3f (0, 0, 0);</span>
  <span class="c1">//Eigen::Vector3f look_at_vector = viewer_pose.rotation () * Eigen::Vector3f (0, 0, 1) + pos_vector;</span>
  <span class="c1">//Eigen::Vector3f up_vector = viewer_pose.rotation () * Eigen::Vector3f (0, -1, 0);</span>
  <span class="c1">//viewer.setCameraPosition (pos_vector[0], pos_vector[1], pos_vector[2],</span>
                            <span class="c1">//look_at_vector[0], look_at_vector[1], look_at_vector[2],</span>
                            <span class="c1">//up_vector[0], up_vector[1], up_vector[2]);</span>
<span class="c1">//}</span>

<span class="c1">// --------------</span>
<span class="c1">// -----Main-----</span>
<span class="c1">// --------------</span>
<span class="kt">int</span> 
<span class="nf">main</span> <span class="p">(</span><span class="kt">int</span> <span class="n">argc</span><span class="p">,</span> <span class="kt">char</span><span class="o">**</span> <span class="n">argv</span><span class="p">)</span>
<span class="p">{</span>
  <span class="c1">// --------------------------------------</span>
  <span class="c1">// -----Parse Command Line Arguments-----</span>
  <span class="c1">// --------------------------------------</span>
  <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">console</span><span class="o">::</span><span class="n">find_argument</span> <span class="p">(</span><span class="n">argc</span><span class="p">,</span> <span class="n">argv</span><span class="p">,</span> <span class="s">&quot;-h&quot;</span><span class="p">)</span> <span class="o">&gt;=</span> <span class="mi">0</span><span class="p">)</span>
  <span class="p">{</span>
    <span class="n">printUsage</span> <span class="p">(</span><span class="n">argv</span><span class="p">[</span><span class="mi">0</span><span class="p">]);</span>
    <span class="k">return</span> <span class="mi">0</span><span class="p">;</span>
  <span class="p">}</span>
  <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">console</span><span class="o">::</span><span class="n">find_argument</span> <span class="p">(</span><span class="n">argc</span><span class="p">,</span> <span class="n">argv</span><span class="p">,</span> <span class="s">&quot;-m&quot;</span><span class="p">)</span> <span class="o">&gt;=</span> <span class="mi">0</span><span class="p">)</span>
  <span class="p">{</span>
    <span class="n">setUnseenToMaxRange</span> <span class="o">=</span> <span class="nb">true</span><span class="p">;</span>
    <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Setting unseen values in range image to maximum range readings.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
  <span class="p">}</span>
  <span class="kt">int</span> <span class="n">tmp_coordinate_frame</span><span class="p">;</span>
  <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">console</span><span class="o">::</span><span class="n">parse</span> <span class="p">(</span><span class="n">argc</span><span class="p">,</span> <span class="n">argv</span><span class="p">,</span> <span class="s">&quot;-c&quot;</span><span class="p">,</span> <span class="n">tmp_coordinate_frame</span><span class="p">)</span> <span class="o">&gt;=</span> <span class="mi">0</span><span class="p">)</span>
  <span class="p">{</span>
    <span class="n">coordinate_frame</span> <span class="o">=</span> <span class="n">pcl</span><span class="o">::</span><span class="n">RangeImage</span><span class="o">::</span><span class="n">CoordinateFrame</span> <span class="p">(</span><span class="n">tmp_coordinate_frame</span><span class="p">);</span>
    <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Using coordinate frame &quot;</span><span class="o">&lt;&lt;</span> <span class="p">(</span><span class="kt">int</span><span class="p">)</span><span class="n">coordinate_frame</span><span class="o">&lt;&lt;</span><span class="s">&quot;.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
  <span class="p">}</span>
  <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">console</span><span class="o">::</span><span class="n">parse</span> <span class="p">(</span><span class="n">argc</span><span class="p">,</span> <span class="n">argv</span><span class="p">,</span> <span class="s">&quot;-s&quot;</span><span class="p">,</span> <span class="n">support_size</span><span class="p">)</span> <span class="o">&gt;=</span> <span class="mi">0</span><span class="p">)</span>
    <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Setting support size to &quot;</span><span class="o">&lt;&lt;</span><span class="n">support_size</span><span class="o">&lt;&lt;</span><span class="s">&quot;.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
  <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">console</span><span class="o">::</span><span class="n">parse</span> <span class="p">(</span><span class="n">argc</span><span class="p">,</span> <span class="n">argv</span><span class="p">,</span> <span class="s">&quot;-r&quot;</span><span class="p">,</span> <span class="n">angular_resolution</span><span class="p">)</span> <span class="o">&gt;=</span> <span class="mi">0</span><span class="p">)</span>
    <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Setting angular resolution to &quot;</span><span class="o">&lt;&lt;</span><span class="n">angular_resolution</span><span class="o">&lt;&lt;</span><span class="s">&quot;deg.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
  <span class="n">angular_resolution</span> <span class="o">=</span> <span class="n">pcl</span><span class="o">::</span><span class="n">deg2rad</span> <span class="p">(</span><span class="n">angular_resolution</span><span class="p">);</span>
  
  <span class="c1">// ------------------------------------------------------------------</span>
  <span class="c1">// -----Read pcd file or create example point cloud if not given-----</span>
  <span class="c1">// ------------------------------------------------------------------</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointType</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">point_cloud_ptr</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointType</span><span class="o">&gt;</span><span class="p">);</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">PointType</span><span class="o">&gt;&amp;</span> <span class="n">point_cloud</span> <span class="o">=</span> <span class="o">*</span><span class="n">point_cloud_ptr</span><span class="p">;</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointWithViewpoint</span><span class="o">&gt;</span> <span class="n">far_ranges</span><span class="p">;</span>
  <span class="n">Eigen</span><span class="o">::</span><span class="n">Affine3f</span> <span class="n">scene_sensor_pose</span> <span class="p">(</span><span class="n">Eigen</span><span class="o">::</span><span class="n">Affine3f</span><span class="o">::</span><span class="n">Identity</span> <span class="p">());</span>
  <span class="n">std</span><span class="o">::</span><span class="n">vector</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">pcd_filename_indices</span> <span class="o">=</span> <span class="n">pcl</span><span class="o">::</span><span class="n">console</span><span class="o">::</span><span class="n">parse_file_extension_argument</span> <span class="p">(</span><span class="n">argc</span><span class="p">,</span> <span class="n">argv</span><span class="p">,</span> <span class="s">&quot;pcd&quot;</span><span class="p">);</span>
  <span class="k">if</span> <span class="p">(</span><span class="o">!</span><span class="n">pcd_filename_indices</span><span class="p">.</span><span class="n">empty</span> <span class="p">())</span>
  <span class="p">{</span>
    <span class="n">std</span><span class="o">::</span><span class="n">string</span> <span class="n">filename</span> <span class="o">=</span> <span class="n">argv</span><span class="p">[</span><span class="n">pcd_filename_indices</span><span class="p">[</span><span class="mi">0</span><span class="p">]];</span>
    <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">io</span><span class="o">::</span><span class="n">loadPCDFile</span> <span class="p">(</span><span class="n">filename</span><span class="p">,</span> <span class="n">point_cloud</span><span class="p">)</span> <span class="o">==</span> <span class="o">-</span><span class="mi">1</span><span class="p">)</span>
    <span class="p">{</span>
      <span class="n">std</span><span class="o">::</span><span class="n">cerr</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Was not able to open file </span><span class="se">\&quot;</span><span class="s">&quot;</span><span class="o">&lt;&lt;</span><span class="n">filename</span><span class="o">&lt;&lt;</span><span class="s">&quot;</span><span class="se">\&quot;</span><span class="s">.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
      <span class="n">printUsage</span> <span class="p">(</span><span class="n">argv</span><span class="p">[</span><span class="mi">0</span><span class="p">]);</span>
      <span class="k">return</span> <span class="mi">0</span><span class="p">;</span>
    <span class="p">}</span>
    <span class="n">scene_sensor_pose</span> <span class="o">=</span> <span class="n">Eigen</span><span class="o">::</span><span class="n">Affine3f</span> <span class="p">(</span><span class="n">Eigen</span><span class="o">::</span><span class="n">Translation3f</span> <span class="p">(</span><span class="n">point_cloud</span><span class="p">.</span><span class="n">sensor_origin_</span><span class="p">[</span><span class="mi">0</span><span class="p">],</span>
                                                               <span class="n">point_cloud</span><span class="p">.</span><span class="n">sensor_origin_</span><span class="p">[</span><span class="mi">1</span><span class="p">],</span>
                                                               <span class="n">point_cloud</span><span class="p">.</span><span class="n">sensor_origin_</span><span class="p">[</span><span class="mi">2</span><span class="p">]))</span> <span class="o">*</span>
                        <span class="n">Eigen</span><span class="o">::</span><span class="n">Affine3f</span> <span class="p">(</span><span class="n">point_cloud</span><span class="p">.</span><span class="n">sensor_orientation_</span><span class="p">);</span>
    <span class="n">std</span><span class="o">::</span><span class="n">string</span> <span class="n">far_ranges_filename</span> <span class="o">=</span> <span class="n">pcl</span><span class="o">::</span><span class="n">getFilenameWithoutExtension</span> <span class="p">(</span><span class="n">filename</span><span class="p">)</span><span class="o">+</span><span class="s">&quot;_far_ranges.pcd&quot;</span><span class="p">;</span>
    <span class="k">if</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">io</span><span class="o">::</span><span class="n">loadPCDFile</span> <span class="p">(</span><span class="n">far_ranges_filename</span><span class="p">.</span><span class="n">c_str</span> <span class="p">(),</span> <span class="n">far_ranges</span><span class="p">)</span> <span class="o">==</span> <span class="o">-</span><span class="mi">1</span><span class="p">)</span>
      <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Far ranges file </span><span class="se">\&quot;</span><span class="s">&quot;</span><span class="o">&lt;&lt;</span><span class="n">far_ranges_filename</span><span class="o">&lt;&lt;</span><span class="s">&quot;</span><span class="se">\&quot;</span><span class="s"> does not exists.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
  <span class="p">}</span>
  <span class="k">else</span>
  <span class="p">{</span>
    <span class="n">setUnseenToMaxRange</span> <span class="o">=</span> <span class="nb">true</span><span class="p">;</span>
    <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;</span><span class="se">\n</span><span class="s">No *.pcd file given =&gt; Generating example point cloud.</span><span class="se">\n\n</span><span class="s">&quot;</span><span class="p">;</span>
    <span class="k">for</span> <span class="p">(</span><span class="kt">float</span> <span class="n">x</span><span class="o">=-</span><span class="mf">0.5f</span><span class="p">;</span> <span class="n">x</span><span class="o">&lt;=</span><span class="mf">0.5f</span><span class="p">;</span> <span class="n">x</span><span class="o">+=</span><span class="mf">0.01f</span><span class="p">)</span>
    <span class="p">{</span>
      <span class="k">for</span> <span class="p">(</span><span class="kt">float</span> <span class="n">y</span><span class="o">=-</span><span class="mf">0.5f</span><span class="p">;</span> <span class="n">y</span><span class="o">&lt;=</span><span class="mf">0.5f</span><span class="p">;</span> <span class="n">y</span><span class="o">+=</span><span class="mf">0.01f</span><span class="p">)</span>
      <span class="p">{</span>
        <span class="n">PointType</span> <span class="n">point</span><span class="p">;</span>  <span class="n">point</span><span class="p">.</span><span class="n">x</span> <span class="o">=</span> <span class="n">x</span><span class="p">;</span>  <span class="n">point</span><span class="p">.</span><span class="n">y</span> <span class="o">=</span> <span class="n">y</span><span class="p">;</span>  <span class="n">point</span><span class="p">.</span><span class="n">z</span> <span class="o">=</span> <span class="mf">2.0f</span> <span class="o">-</span> <span class="n">y</span><span class="p">;</span>
        <span class="n">point_cloud</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">push_back</span> <span class="p">(</span><span class="n">point</span><span class="p">);</span>
      <span class="p">}</span>
    <span class="p">}</span>
    <span class="n">point_cloud</span><span class="p">.</span><span class="n">width</span> <span class="o">=</span> <span class="p">(</span><span class="kt">int</span><span class="p">)</span> <span class="n">point_cloud</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span>  <span class="n">point_cloud</span><span class="p">.</span><span class="n">height</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span>
  <span class="p">}</span>
  
  <span class="c1">// -----------------------------------------------</span>
  <span class="c1">// -----Create RangeImage from the PointCloud-----</span>
  <span class="c1">// -----------------------------------------------</span>
  <span class="kt">float</span> <span class="n">noise_level</span> <span class="o">=</span> <span class="mf">0.0</span><span class="p">;</span>
  <span class="kt">float</span> <span class="n">min_range</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
  <span class="kt">int</span> <span class="n">border_size</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">RangeImage</span><span class="o">::</span><span class="n">Ptr</span> <span class="n">range_image_ptr</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">RangeImage</span><span class="p">);</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">RangeImage</span><span class="o">&amp;</span> <span class="n">range_image</span> <span class="o">=</span> <span class="o">*</span><span class="n">range_image_ptr</span><span class="p">;</span>   
  <span class="n">range_image</span><span class="p">.</span><span class="n">createFromPointCloud</span> <span class="p">(</span><span class="n">point_cloud</span><span class="p">,</span> <span class="n">angular_resolution</span><span class="p">,</span> <span class="n">pcl</span><span class="o">::</span><span class="n">deg2rad</span> <span class="p">(</span><span class="mf">360.0f</span><span class="p">),</span> <span class="n">pcl</span><span class="o">::</span><span class="n">deg2rad</span> <span class="p">(</span><span class="mf">180.0f</span><span class="p">),</span>
                                   <span class="n">scene_sensor_pose</span><span class="p">,</span> <span class="n">coordinate_frame</span><span class="p">,</span> <span class="n">noise_level</span><span class="p">,</span> <span class="n">min_range</span><span class="p">,</span> <span class="n">border_size</span><span class="p">);</span>
  <span class="n">range_image</span><span class="p">.</span><span class="n">integrateFarRanges</span> <span class="p">(</span><span class="n">far_ranges</span><span class="p">);</span>
  <span class="k">if</span> <span class="p">(</span><span class="n">setUnseenToMaxRange</span><span class="p">)</span>
    <span class="n">range_image</span><span class="p">.</span><span class="n">setUnseenToMaxRange</span> <span class="p">();</span>
  
  <span class="c1">// --------------------------------------------</span>
  <span class="c1">// -----Open 3D viewer and add point cloud-----</span>
  <span class="c1">// --------------------------------------------</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">visualization</span><span class="o">::</span><span class="n">PCLVisualizer</span> <span class="n">viewer</span> <span class="p">(</span><span class="s">&quot;3D Viewer&quot;</span><span class="p">);</span>
  <span class="n">viewer</span><span class="p">.</span><span class="n">setBackgroundColor</span> <span class="p">(</span><span class="mi">1</span><span class="p">,</span> <span class="mi">1</span><span class="p">,</span> <span class="mi">1</span><span class="p">);</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">visualization</span><span class="o">::</span><span class="n">PointCloudColorHandlerCustom</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointWithRange</span><span class="o">&gt;</span> <span class="n">range_image_color_handler</span> <span class="p">(</span><span class="n">range_image_ptr</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">);</span>
  <span class="n">viewer</span><span class="p">.</span><span class="n">addPointCloud</span> <span class="p">(</span><span class="n">range_image_ptr</span><span class="p">,</span> <span class="n">range_image_color_handler</span><span class="p">,</span> <span class="s">&quot;range image&quot;</span><span class="p">);</span>
  <span class="n">viewer</span><span class="p">.</span><span class="n">setPointCloudRenderingProperties</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">visualization</span><span class="o">::</span><span class="n">PCL_VISUALIZER_POINT_SIZE</span><span class="p">,</span> <span class="mi">1</span><span class="p">,</span> <span class="s">&quot;range image&quot;</span><span class="p">);</span>
  <span class="c1">//viewer.addCoordinateSystem (1.0f, &quot;global&quot;);</span>
  <span class="c1">//PointCloudColorHandlerCustom&lt;PointType&gt; point_cloud_color_handler (point_cloud_ptr, 150, 150, 150);</span>
  <span class="c1">//viewer.addPointCloud (point_cloud_ptr, point_cloud_color_handler, &quot;original point cloud&quot;);</span>
  <span class="n">viewer</span><span class="p">.</span><span class="n">initCameraParameters</span> <span class="p">();</span>
  <span class="c1">//setViewerPose (viewer, range_image.getTransformationToWorldSystem ());</span>
  
  <span class="c1">// --------------------------</span>
  <span class="c1">// -----Show range image-----</span>
  <span class="c1">// --------------------------</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">visualization</span><span class="o">::</span><span class="n">RangeImageVisualizer</span> <span class="n">range_image_widget</span> <span class="p">(</span><span class="s">&quot;Range image&quot;</span><span class="p">);</span>
  <span class="n">range_image_widget</span><span class="p">.</span><span class="n">showRangeImage</span> <span class="p">(</span><span class="n">range_image</span><span class="p">);</span>
  
  <span class="c1">// --------------------------------</span>
  <span class="c1">// -----Extract NARF keypoints-----</span>
  <span class="c1">// --------------------------------</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">RangeImageBorderExtractor</span> <span class="n">range_image_border_extractor</span><span class="p">;</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">NarfKeypoint</span> <span class="n">narf_keypoint_detector</span> <span class="p">(</span><span class="o">&amp;</span><span class="n">range_image_border_extractor</span><span class="p">);</span>
  <span class="n">narf_keypoint_detector</span><span class="p">.</span><span class="n">setRangeImage</span> <span class="p">(</span><span class="o">&amp;</span><span class="n">range_image</span><span class="p">);</span>
  <span class="n">narf_keypoint_detector</span><span class="p">.</span><span class="n">getParameters</span> <span class="p">().</span><span class="n">support_size</span> <span class="o">=</span> <span class="n">support_size</span><span class="p">;</span>
  <span class="c1">//narf_keypoint_detector.getParameters ().add_points_on_straight_edges = true;</span>
  <span class="c1">//narf_keypoint_detector.getParameters ().distance_for_additional_points = 0.5;</span>
  
  <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">keypoint_indices</span><span class="p">;</span>
  <span class="n">narf_keypoint_detector</span><span class="p">.</span><span class="n">compute</span> <span class="p">(</span><span class="n">keypoint_indices</span><span class="p">);</span>
  <span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Found &quot;</span><span class="o">&lt;&lt;</span><span class="n">keypoint_indices</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">()</span><span class="o">&lt;&lt;</span><span class="s">&quot; key points.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>

  <span class="c1">// ----------------------------------------------</span>
  <span class="c1">// -----Show keypoints in range image widget-----</span>
  <span class="c1">// ----------------------------------------------</span>
  <span class="c1">//for (std::size_t i=0; i&lt;keypoint_indices.points.size (); ++i)</span>
    <span class="c1">//range_image_widget.markPoint (keypoint_indices.points[i]%range_image.width,</span>
                                  <span class="c1">//keypoint_indices.points[i]/range_image.width);</span>
  
  <span class="c1">// -------------------------------------</span>
  <span class="c1">// -----Show keypoints in 3D viewer-----</span>
  <span class="c1">// -------------------------------------</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span><span class="o">&gt;::</span><span class="n">Ptr</span> <span class="n">keypoints_ptr</span> <span class="p">(</span><span class="k">new</span> <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span><span class="o">&gt;</span><span class="p">);</span>
  <span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span><span class="o">&gt;&amp;</span> <span class="n">keypoints</span> <span class="o">=</span> <span class="o">*</span><span class="n">keypoints_ptr</span><span class="p">;</span>
  <span class="n">keypoints</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">resize</span> <span class="p">(</span><span class="n">keypoint_indices</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">());</span>
  <span class="k">for</span> <span class="p">(</span><span class="n">std</span><span class="o">::</span><span class="kt">size_t</span> <span class="n">i</span><span class="o">=</span><span class="mi">0</span><span class="p">;</span> <span class="n">i</span><span class="o">&lt;</span><span class="n">keypoint_indices</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">();</span> <span class="o">++</span><span class="n">i</span><span class="p">)</span>
    <span class="n">keypoints</span><span class="p">.</span><span class="n">points</span><span class="p">[</span><span class="n">i</span><span class="p">].</span><span class="n">getVector3fMap</span> <span class="p">()</span> <span class="o">=</span> <span class="n">range_image</span><span class="p">.</span><span class="n">points</span><span class="p">[</span><span class="n">keypoint_indices</span><span class="p">.</span><span class="n">points</span><span class="p">[</span><span class="n">i</span><span class="p">]].</span><span class="n">getVector3fMap</span> <span class="p">();</span>

  <span class="n">pcl</span><span class="o">::</span><span class="n">visualization</span><span class="o">::</span><span class="n">PointCloudColorHandlerCustom</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span><span class="o">&gt;</span> <span class="n">keypoints_color_handler</span> <span class="p">(</span><span class="n">keypoints_ptr</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">255</span><span class="p">,</span> <span class="mi">0</span><span class="p">);</span>
  <span class="n">viewer</span><span class="p">.</span><span class="n">addPointCloud</span><span class="o">&lt;</span><span class="n">pcl</span><span class="o">::</span><span class="n">PointXYZ</span><span class="o">&gt;</span> <span class="p">(</span><span class="n">keypoints_ptr</span><span class="p">,</span> <span class="n">keypoints_color_handler</span><span class="p">,</span> <span class="s">&quot;keypoints&quot;</span><span class="p">);</span>
  <span class="n">viewer</span><span class="p">.</span><span class="n">setPointCloudRenderingProperties</span> <span class="p">(</span><span class="n">pcl</span><span class="o">::</span><span class="n">visualization</span><span class="o">::</span><span class="n">PCL_VISUALIZER_POINT_SIZE</span><span class="p">,</span> <span class="mi">7</span><span class="p">,</span> <span class="s">&quot;keypoints&quot;</span><span class="p">);</span>
  
  <span class="c1">//--------------------</span>
  <span class="c1">// -----Main loop-----</span>
  <span class="c1">//--------------------</span>
  <span class="k">while</span> <span class="p">(</span><span class="o">!</span><span class="n">viewer</span><span class="p">.</span><span class="n">wasStopped</span> <span class="p">())</span>
  <span class="p">{</span>
    <span class="n">range_image_widget</span><span class="p">.</span><span class="n">spinOnce</span> <span class="p">();</span>  <span class="c1">// process GUI events</span>
    <span class="n">viewer</span><span class="p">.</span><span class="n">spinOnce</span> <span class="p">();</span>
    <span class="n">pcl_sleep</span><span class="p">(</span><span class="mf">0.01</span><span class="p">);</span>
  <span class="p">}</span>
<span class="p">}</span>
</pre></div>
</td></tr></table></div>
</div>
<div class="section" id="explanation">
<h1>Explanation</h1>
<p>In the beginning we do command line parsing, read a point cloud from disc (or
create it if not provided), create a range image and visualize it. All of these
steps are already covered in the previous tutorial <a class="reference external" href="http://www.pointclouds.org/documentation/tutorials/range_image_visualization.php#range-image-visualization">Range image visualization</a> .</p>
<p>The interesting part begins here:</p>
<div class="highlight-cpp notranslate"><div class="highlight"><pre><span></span><span class="p">...</span>
<span class="n">pcl</span><span class="o">::</span><span class="n">RangeImageBorderExtractor</span> <span class="n">range_image_border_extractor</span><span class="p">;</span>
<span class="n">pcl</span><span class="o">::</span><span class="n">NarfKeypoint</span> <span class="n">narf_keypoint_detector</span> <span class="p">(</span><span class="o">&amp;</span><span class="n">range_image_border_extractor</span><span class="p">);</span>
<span class="n">narf_keypoint_detector</span><span class="p">.</span><span class="n">setRangeImage</span> <span class="p">(</span><span class="o">&amp;</span><span class="n">range_image</span><span class="p">);</span>
<span class="n">narf_keypoint_detector</span><span class="p">.</span><span class="n">getParameters</span> <span class="p">().</span><span class="n">support_size</span> <span class="o">=</span> <span class="n">support_size</span><span class="p">;</span>
<span class="c1">//narf_keypoint_detector.getParameters ().add_points_on_straight_edges = true;</span>
<span class="c1">//narf_keypoint_detector.getParameters ().distance_for_additional_points = 0.5;</span>

<span class="n">pcl</span><span class="o">::</span><span class="n">PointCloud</span><span class="o">&lt;</span><span class="kt">int</span><span class="o">&gt;</span> <span class="n">keypoint_indices</span><span class="p">;</span>
<span class="n">narf_keypoint_detector</span><span class="p">.</span><span class="n">compute</span> <span class="p">(</span><span class="n">keypoint_indices</span><span class="p">);</span>
<span class="n">std</span><span class="o">::</span><span class="n">cout</span> <span class="o">&lt;&lt;</span> <span class="s">&quot;Found &quot;</span><span class="o">&lt;&lt;</span><span class="n">keypoint_indices</span><span class="p">.</span><span class="n">points</span><span class="p">.</span><span class="n">size</span> <span class="p">()</span><span class="o">&lt;&lt;</span><span class="s">&quot; key points.</span><span class="se">\n</span><span class="s">&quot;</span><span class="p">;</span>
<span class="p">...</span>
</pre></div>
</div>
<p>This creates a RangeImageBorderExtractor object, that is needed for the
interest point extraction. If you are interested in this you can have a look at
the Range Image Border Extraction tutorial. In this case we just use the
RangeImageBorderExtractor object with its default parameters. Then we create
the NarfKeypoint object, give it the RangeImageBorderExtractor object, the
range image and set the support size (the size of the sphere around a point
that includes points that are used for the determination of the interest
value). The commented out part contains some parameters that you can test out
if you want. Next we create the object where the indices of the determined
keypoints will be saved and compute them. In the last step we output the number
of found keypoints.</p>
<p>The remaining code just visualizes the results in a range image widget and also in a 3D viewer.</p>
</div>
<div class="section" id="compiling-and-running-the-program">
<h1>Compiling and running the program</h1>
<p>Add the following lines to your CMakeLists.txt file:</p>
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
12</pre></div></td><td class="code"><div class="highlight"><pre><span></span><span class="nb">cmake_minimum_required</span><span class="p">(</span><span class="s">VERSION</span> <span class="s">2.6</span> <span class="s">FATAL_ERROR</span><span class="p">)</span>

<span class="nb">project</span><span class="p">(</span><span class="s">narf_keypoint_extraction</span><span class="p">)</span>

<span class="nb">find_package</span><span class="p">(</span><span class="s">PCL</span> <span class="s">1.3</span> <span class="s">REQUIRED</span><span class="p">)</span>

<span class="nb">include_directories</span><span class="p">(</span><span class="o">${</span><span class="nv">PCL_INCLUDE_DIRS</span><span class="o">}</span><span class="p">)</span>
<span class="nb">link_directories</span><span class="p">(</span><span class="o">${</span><span class="nv">PCL_LIBRARY_DIRS</span><span class="o">}</span><span class="p">)</span>
<span class="nb">add_definitions</span><span class="p">(</span><span class="o">${</span><span class="nv">PCL_DEFINITIONS</span><span class="o">}</span><span class="p">)</span>

<span class="nb">add_executable</span> <span class="p">(</span><span class="s">narf_keypoint_extraction</span> <span class="s">narf_keypoint_extraction.cpp</span><span class="p">)</span>
<span class="nb">target_link_libraries</span> <span class="p">(</span><span class="s">narf_keypoint_extraction</span> <span class="o">${</span><span class="nv">PCL_LIBRARIES</span><span class="o">}</span><span class="p">)</span>
</pre></div>
</td></tr></table></div>
<p>After you have made the executable, you can run it. Simply do:</p>
<div class="highlight-default notranslate"><div class="highlight"><pre><span></span>$ ./narf_keypoint_extraction -m
</pre></div>
</div>
<p>This will use an autogenerated point cloud of a rectangle floating in space.
The key points are detected in the corners. The parameter -m is necessary,
since the area around the rectangle is unseen and therefore the system can not
detect it as a border. The option -m changes the unseen area to maximum range
readings, thereby enabling the system to use these borders.</p>
<p>You can also try it with a point cloud file from your hard drive:</p>
<div class="highlight-default notranslate"><div class="highlight"><pre><span></span>$ ./narf_keypoint_extraction &lt;point_cloud.pcd&gt;
</pre></div>
</div>
<p>The output should look similar to this:</p>
<img alt="_images/narf_keypoint_extraction.png" src="_images/narf_keypoint_extraction.png" />
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