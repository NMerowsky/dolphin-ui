---
output: pdf_document
---
# DEBrowser: 
Interactive Differential Expression Analysis Tool

# Introduction

Differential gene expression analysis has become an increasingly popular tool
in determining and viewing up and/or down experssed genes between two sets of
samples.  The goal of Differential gene expression analysis is to find genes
or transcripts whose difference in expression, when accounting for the
variance within condition, is higher than expected by chance.  DESeq2
<https://bioconductor.org/packages/release/bioc/html/DESeq2.html> is an R
package available via Bioconductor and is designed to normalize count data
from high-throughput sequencing assays such as RNA-Seq and test for
differential expression (Love et al. 2014).  With multiple parameters such as
padjust values, log fold changes, plot styles, and so on, altering plots
created with your DE data can be a hassle as well as time consuming. The
Differential Expression Browser uses DESeq2 coupled with shiny to produce
real-time changes within your plot queries and allows for interactive browsing
of your DESeq results. In addition to DESeq analysis, DEBrowser also offers a
variety of other plots and analysis tools to help visualize your data even
further.

# Quick start

Before you start;
First, you will have to install R and/or RStudio.
(On Fedora/Red Hat/CentOS, these packages have to be installed;
openssl-devel, libxml2-devel, libcurl-devel, libpng-devel)


Running these simple commands will launch the DEBrowser within your local
machine:

```
# Installation instructions:
# 1. Install DEBrowser and its dependencies by running the lines below
#    in R or RStudio.

source(“http://www.bioconductor.org/biocLite.R”)

biocLite("debrowser")

# 2. Load the library

library(DEBrowser)

# 3. Start DEBrowser

startDEBrowser()
```

# Browsing your Data

Once you have the DEBrowser running, a page will load asking to choose a CSV
file or to load the demo data.  In order to run DESeq2, we are going to need
gene quantifications for those genes contained in a CSV or TSV format.

IE:

```
gene  transcript  exper_rep1 exper_rep2 control_rep1 control_rep2
DQ714826  uc007tfl.1  0.00  0.00  0.00  0.00
DQ551521  uc008bml.1  0.00  0.00  0.00  0.00
AK028549  uc011wpi.1  2.00  1.29  0.00  0.00
```

You can also view/use the demo data by clicking the 'Load Demo!' text as an
example.  For the case study demo, feel free to download our case study demo
file at <http://galaxyweb.umassmed.edu/pub/DC/advanced_demo.tsv>
After obtaining and loading in the gene quantifications file, you
are then able to view QC information of your quantifications or to continue
on to running DESeq2.

![alt text](https://i.imgur.com/CkPTon9.png "Initial option selection")

Figure 1: The initial options selection.

Upon selection of QC information, the user will be
show an all-to-all plot of their samples.  This sample-by-sample comparison
will help you visualize possible descrepencies between replicate samples,
in case you may want to omit them for further analysis.  To the left of
this plot are various plot-shaping options you can alter to more easily view
the all-to-all plot

You can also view
the genes within your quantification file in various ways.  The 'All Detected'
tab will list all of the genes present within your file. The 'Gene Set' tab
displays all of the genes within a specific search.  You can enter genes into
the search box by selecting the 'geneset' dropdown option on the left.  The
Last tab 'Most Varied' will display your top N varied genes.  You can alter
the value of N by selecting 'most-varied' from the dropdown menu on the left.

![alt text](https://i.imgur.com/rPBdUX4.png "QC plots")

Figure 2: Display of the initial QC plots page.

Upon selecting to run DESeq, you are then able to select
which samples will be selected for your first condition and second condition
to use for differential expression analysis.  By clicking the 'Add New
Comparison' button, you can add as many different comparisons as you want.
Sample names are created based on the column headers within your data file.
Once you've selected your comparisons, you are then ready to run DESeq2 to
calculate differential expression by clicking on the 'Submit!' button.

![alt text](https://i.imgur.com/XhxEfJO.png "Loading in samples")

Figure 3: Menus after loading in a sample.

#	Analyzing the Results

After clicking on the 'Submit!' button, DESeq2 will analyze your comparisons
and store the results into seperate data tables.  Shiny will then allow you
to access this data, with multiple interactive features, at the click of a
button.  It is important to note that the resulting data produced from DESeq
is normalized.

Upon finishing the DESeq analysis, a tab based menu will appear with multiple
options.

![alt text](https://i.imgur.com/yKQYQOl.png 'tab selection')

Figure 4: List of the tabbed menus in DEBrowser.

The first tab, the 'Main Plots' section, is where you will be able to view
the interactive results plots.  On the left hand side of the screen will be
the options you have  to alter the padj and fold change
cutoff values, what specific data set to use such as up or down regulated
genes, what comparison dataset you would like to use to plot,
and what type of plot you would like to view your results in.  Plot
choices include:

![alt text](https://i.imgur.com/VrtRmur.png 'scatterplot')

Figure 5: Main scatter plot.

![alt text](https://i.imgur.com/yyqca4N.png 'scatterplot')

Figure 6: Main volcano Plot.

![alt text](https://i.imgur.com/nAlocEa.png 'scatterplot')

Figure 7: Main MA plot.

Once you have selected your values, you can hit the 'Submit!' button to create
your interactive plots!

The top left plot is whichever plot you have
selected to use to analyze your results.  Up-regulated genes are displayed
in green while down-regulated genes are displayed in red.
Hovering over a gene on this plot will display the bottom two plots: the
genes normalized variation and colored by condition in the left graph,
and the normalized variation between conditions within the right graph.
Hovering over a gene will also display information about that gene in
regards to both conditions you have selected.
By clicking and dragging your mouse to create a selection over the main graph,
you will create the top right plot, or the zoomed in version of your
selection.  If you are going to change any of the parameters on the left,
please make sure to re-click the 'Submit!' button to update the graphs.
It's also worth noting that the plots are resizable as well as downloable.

![alt text](https://i.imgur.com/0OYGZTb.png "Main plots")

Figure 8: The main plots page within DEBrowser.

Selecting the 'QC Plots' tab will take you to the quality control plots
section.  These QC plots are very similar to the QC plots shown before
running DESeq, however the dataset being used here depends on the one
you select on the left menu.  In addition to the all-to-all plot shown
within the previous QC analysis, users can also view a heatmap and PCA
plot of their analyzed data by selecting the proper plot on the left
menu.

![alt text](https://i.imgur.com/WAer2hE.png "Heatmap")

Figure 9: Display of the heatmap within DEBrowser.

The heatmap is a great way to analyze replicate results of genes all in
one simple plot.  Users have the option to change the clustering method used
as well as the distance method used to display their heatmap.  In addition,
you can also change the size of the heatmap produced and adjust the p-adjust
and fold change cut off for this plot as well.  Once all of the parameters
have been set, click the 'Submit!' button at the bottom of the left menu to
generate your heatmap.

![alt text](https://i.imgur.com/byp74dP.png "PCA")

Figure 10: Display of the PCA plot within DEBrowser.

Prinicipal Component Analysis (PCA) is another excellent method of checking
replicates.  PCA calculates the variance between all of the samples genes
within your current comparison set and creates a two-dimensional
graph to represent the proportion of variance explained in different
components.  Within the PCA plot section you can select the p-adjust
value, fold change cut off value, which comparison set to use, which dataset
to use, the height and width of the corresponding plots, as well as which
prinicipal components to analyze by changing the appropriate values on the
left menu.

The next tab, 'GO Term', takes you to the ontology comparison portion of
DEBrowser.  From here you can select the standard dataset options such as
p-adjust value, fold change cut off value, which comparison set to use, and
which dataset to use on the left menu.  In addition to these parameters, you
also can choose from the 4 different ontology plot options: 'enrichGO',
'enrichKEGG', 'Disease', and 'compareCluster'.  Selecting one of these plot
options queries their specific databases with your current DESeq results.

![alt text](https://i.imgur.com/43ApxMf.png "GO")

Figure 11: Display of the GO Plots section within DEBrowser.

The next six tabs contain various result information in table formats.
The 'All Detected' tab contains the list of all the genes within the
TSV/CSV provided with the corresponding DESeq analyses.
The 'Up' tab contains a list of all the up-regulated genes based on the
options selected on the left panel.  The 'Down' tab contains a list of all
the down-regulated genes based on the options selected.

![alt text](https://i.imgur.com/2vbVvKO.png "Down Regulated")

Figure 12: Display of the down-regulated genes table.

The 'Selected'
tab contains the list of genes selected from the main plots section.

![alt text](https://i.imgur.com/azLe0FR.png "Selection")

Figure 13: Display of the selected genes table.

The 'Gene Set' tab allows you to filter out gene data based on genes selected.
To create a gene set, on the left panel select the 'geneset' option under the
'Choose a dataset' option.  Once selected. a text box will appear where you
can input various genes of interest.  You can also use regular expressions
in order to search for specific gene sets.

![alt text](https://i.imgur.com/jfTtYmW.png "geneset")

Figure 14: Display of the geneset input box.

The 'Most Varied' tab, much like the original QC 'Most Varied' tab, allows
you to view the list of most varied genes based on user input parameters on
the left panel.  The Last tab, 'Comparisons', allows you to view the
differences between your different condition comparisons.

![alt text](https://i.imgur.com/Ki0f5Uo.png "Comparisons")

Figure 15: Condition comparisons table within DEBrowser.

Lastly, the tables have a bunch of features that allow you to view your DESeq
results more conviently.  By clicking on a column header, you can sort the
data within the table based either alphabetical or numeric sorting.
You can also enter a term within the search box to search for a specific
gene within the table.

With that, you've now successfully navigated the DEBrowser and are ready to
start inserting your own data files and browsing your own experiments.  Enjoy
the DEBrowser!
