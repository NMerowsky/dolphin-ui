from os import environ, pathsep, X_OK, access
from os.path import join, isfile
from tempfile import NamedTemporaryFile
from glob import glob

class NoPathFoundError( Exception ) :
    pass

def which(filename):
    """A unix which command like function in the shell"""
    locations = environ.get("PATH").split(pathsep)
    candidates = []
    for location in locations:
        candidate = join(location, filename)
        if isfile(candidate) and access(candidate, X_OK) :
            candidates.append(candidate)

    if candidates :
        return candidates
    else :
        raise NoPathFoundError( filename )

class TooManyPathFoundError( Exception ) :
	pass

def find_analysis_dir( dir='.', prefix='analysis_' ) :
	'''returns analysis directory'''
	template = join(dir,prefix+'*')
	analysis_dirs = glob( template )

	if len(analysis_dirs) == 1 :
		return analysis_dirs[0]
	elif len(analysis_dirs) == 0 :
		raise NoPathFoundError( template )
	else :
		raise TooManyPathFoundError( Exception )
		

def build_description_file( description, fp=None, encoded_content=0 ) :
	'''returns a temporary file pointer if file pointer is not given'''
	if not fp :
		fp = NamedTemporaryFile()
	
	print >>fp, '\t'.join( ['Name', 'File', 'Geometry', 'Barcodes', '5p_ad', '3p_ad', '5p_seq_insert', '3p_seq_insert'] )
	if encoded_content :
		fp.write( description.decode('base64') )
	else :
		for line in description.split(':') :
			print >>fp, line.replace( ',', '\t' ).replace( '+', ',' )

	fp.flush()
	return fp
