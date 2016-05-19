#!/usr/bin/env perl

#########################################################################################
#                                       stepCounts.pl
#########################################################################################
# 
#  This program prepares count and summary files in sequential mapping step 
#
#
#########################################################################################
# AUTHORS:
#
# Alper Kucukural, PhD 
# Jul 4, 2014
#########################################################################################


############## LIBRARIES AND PRAGMAS ################

 use List::Util qw[min max];
 use strict;
 use File::Basename;
 use Getopt::Long;
 use Pod::Usage; 

#################### VARIABLES ######################
 my $mapnames         = "";
 my $outdir           = "";
 my $jobsubmit        = "";
 my $indexcmd         = "";
 my $pubdir           = "";
 my $wkey             = "";
 my $bedmake          = "";
 my $gcommondb        = "";
 my $cmd              = ""; 
 my $servicename      = "";
 my $help             = "";
 my $print_version    = "";
 my $version          = "dolphin_counts v1.0.0";

################### PARAMETER PARSING ####################

my $command=$0." ".join(" ",@ARGV); ####command line copy

GetOptions( 
        'mapnames=s'     => \$mapnames,
	    'outdir=s'       => \$outdir,
        'cmd=s'          => \$cmd,
        'bedmake=s'      => \$bedmake,
        'indexcmd=s'     => \$indexcmd,
        'gcommondb=s'    => \$gcommondb,
        'pubdir=s'       => \$pubdir,
        'wkey=s'         => \$wkey,
        'servicename=s'  => \$servicename,
        'jobsubmit=s'    => \$jobsubmit,
	'help'           => \$help, 
	'version'        => \$print_version,
) or die("Unrecognized optioins.\nFor help, run this script with -help option.\n");

if($help){
    pod2usage( {
		'-verbose' => 2, 
		'-exitval' => 1,
	} );
}

if($print_version){
  print "Version ".$version."\n";
  exit;
}

pod2usage( {'-verbose' => 0, '-exitval' => 1,} ) if ( ($mapnames eq "") or ($outdir eq "") );	

################### MAIN PROGRAM ####################
#  Prepare count and summary files

my $outd   = "$outdir/counts";
`mkdir -p $outd`;
die "Error 15: Cannot create the directory:".$outd if ($?);
my $puboutdir   = "$pubdir/$wkey";
`mkdir -p $puboutdir`;
die "Error 15: Cannot create the directory:".$puboutdir if ($?);
my ($inputdir, $com)=();

my @mapnames_arr=split(/[,\t\s]+/, $mapnames);
foreach my $mapname (@mapnames_arr)
{ 
  my ($name, $bedfile)=();
  my @arr = split(/:/, $mapname);
  $com="";
  if (scalar(@arr)==2)
  {
  # Custom index, it requires the fasta file in the same directory with index file
     $name=$arr[0];
     $mapname=$name;
     my $ind=$arr[1];
     my $fasta=$ind;
     if(checkFile("$ind.fasta"))
     {
       $fasta.=".fasta";
     }
     elsif(checkFile("$ind.fa"))
     {
       $fasta.=".fa";
     }
     else
     {
      die "Error 64: please check the file: $ind.fa or $ind.fasta "; 
     }
     if(!checkFile("$ind.4.bt2"))
     {
        $com="$indexcmd $fasta $ind"; 
     }
     $bedfile="$ind.bed";
     if(!checkFile($bedfile))
     { 
        $com.=" && " if($com !~/^$/);
        $com.="$bedmake $fasta $name>$bedfile";
     }
  }
  else
  {
    # Common index, it requires the fasta file in the same directory with index file
      $name=$mapname;
      $bedfile="$gcommondb/$mapname/$mapname.bed";
      print "MAPNAME:$mapname # $gcommondb\n\n";
      if ($gcommondb=~/$mapname/) #&& checkFile("$gcommondb/$mapname/piRNA.bed") )
      {
         if (checkFile("$gcommondb/$mapname/piRNAcoor.bed"))
         {
           countCov($mapname, "piRNAcoor", "$gcommondb/$mapname/piRNAcoor.bed" , $outdir, $outd, $cmd, "",$version, $puboutdir, $wkey);
         }
         if (checkFile("$gcommondb/$mapname/miRNAcoor.bed"))
         {
           countCov($mapname, "miRNAcoor", "$gcommondb/$mapname/miRNAcoor.bed" , $outdir, $outd, $cmd, "", $version, $puboutdir, $wkey);
         }
      }
  }

countCov($mapname, $name, $bedfile, $outdir, $outd, $cmd, $com, $version, $puboutdir, $wkey);
}

#Copy count directory to its web accessible area
`rm -rf $outd/tmp && cp -R $outd $puboutdir`;  
die "Error 17: Cannot copy the directory:".$puboutdir if ($?);

sub countCov
{
  my ($mapname, $name, $bedfile, $outdir, $outd, $cmd, $precom, $version, $puboutdi, $wkey)=@_; 
  my $inputdir = "$outdir/seqmapping/".lc($mapname);
  print $inputdir."\n";
  my $com=`ls $inputdir/*.sorted.bam 2>&1`;

  print $com."\n";
  if ($com !~/No such file or directory/)
  {

  my @files = split(/[\n\r\s\t,]+/, $com);
  my $filestr="";
  
  my $header="id\tlen";
  foreach my $file (@files)
  {
    $filestr.=$file." ";
    $file=~/.*\/(.*)\.sorted.bam/;
    $header.="\t$1";
  }
  $com="mkdir -p $outd/tmp && "; 
  $com.=$precom." && " if($precom !~/^$/);
  $com.="echo \"$header\">$outd/tmp/$name.header.tsv &&"; 
  $com.= "$cmd -bams $filestr -bed $bedfile >$outd/tmp/$name.counts.tmp && ";
  $com.= "awk -F \"\\t\" \'{a=\"\";for (i=7;i<=NF;i++){a=a\"\\t\"\$i;} print \$4\"\\t\"(\$3-\$2)\"\"a}\' $outd/tmp/$name.counts.tmp> $outd/tmp/$name.counts.tsv && ";
  $com.= "sort -k3,3nr $outd/tmp/$name.counts.tsv>$outd/tmp/$name.sorted.tsv && ";
  $com.= "cat $outd/tmp/$name.header.tsv $outd/tmp/$name.sorted.tsv>$outd/$name.counts.tsv && ";
  $com.= "echo \"File\tTotal Reads\tUnmapped Reads\tReads 1\tReads >1\tTotal align\">$outd/tmp/$name.summary.header && ";
  $com.= "cat $outd/tmp/$name.summary.header $outdir/seqmapping/".lc($name)."/*.sum>$outd/$name.summary.tsv && ";
  $com.= "echo \"$wkey\t$version\tsummary\tcounts/$name.summary.tsv\" >> $puboutdir/reports.tsv && ";
  $com.= "echo \"$wkey\t$version\tcounts\tcounts/$name.counts.tsv\" >> $puboutdir/reports.tsv "; 
  print $com."\n"; 
  `$com`;
  #die "Error 18: Cannot add to the reports!" if ($?);
  }
}


sub checkFile
{
 my ($file) = $_[0];
 return 1 if (-e $file);
 return 0;
}

__END__


=head1 NAME

stepCounts.pl

=head1 SYNOPSIS  

stepCounts.pl -i input <fastq> 
            -o outdir <output directory> 
            -b bowtieCmd <bowtie dir and file> 
            -p params <bowtie params> 
            -r ribosomeInd <ribosome Index file>

stepCounts.pl -help

stepCounts.pl -version

For help, run this script with -help option.

=head1 OPTIONS

=head2 -i  input file <fastq format> 

fastq files has to be separated with ":". If it is paired end the paired end files has to ber separated by ","

Ex: For single end;

test1.fastq:test2.fastq:ctrl1.fastq:ctrl2.fastq

for paired end;

test1_R1.fastq,test1_R2.fastq:ctrl1_R1.fastq,ctrl1_R2.fastq

    
=head2 -o outdir <output directory>

the output files will be "$outdir/after_ribosome" 

=head2 -b bowtieCmd <bowtie dir and file> 

Fullpath of bowtie executable file. Ex: ~/bowtie_dir/bowtie

=head2  -p params <bowtie params> 

Bowtie running parameteres. Ex: "-p 8 -n 2 -l 20 -M 1 -a --strata --best"

=head2  -r ribosomeInd <ribosome Index file>

Ribosomal index files. Ex: ~/bowtie_ind/rRNA


=head2 -help

Display this documentation.

=head2 -version

Display the version

=head1 DESCRIPTION

 This program map the reads to rRNAs and put the rest into other files 

=head1 EXAMPLE


stepCounts.pl -i test1.fastq:test2.fastq:ctrl1.fastq:ctrl2.fastq
            -o ~/out
            -b ~/bowtie_dir/bowtie
            -p "-p 8 -n 2 -l 20 -M 1 -a --strata --best"
            -r ~/bowtie_ind/rRNA

=head1 AUTHORS

 Alper Kucukural, PhD

 
=head1 LICENSE AND COPYING

 This program is free software; you can redistribute it and / or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, a copy is available at
 http://www.gnu.org/licenses/licenses.html


