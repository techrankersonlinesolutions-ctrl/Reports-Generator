import React, { useState, useEffect } from 'react';
import { Folder, File, Code, Copy, Check, Download, AlertCircle } from 'lucide-react';

interface FileNode {
  name: string;
  type: 'file' | 'directory';
  path?: string;
  content?: string;
  children?: FileNode[];
}

export default function PhpSourceViewer() {
  const [fileTree, setFileTree] = useState<FileNode[]>([]);
  const [selectedFile, setSelectedFile] = useState<FileNode | null>(null);
  const [copied, setCopied] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [expandedDirs, setExpandedDirs] = useState<Record<string, boolean>>({
    'database': true,
    'config': true,
    'models': true,
    'controllers': true,
    'views': true,
    'views/layout': true,
    'views/report': true,
  });

  useEffect(() => {
    fetch('/api/php-codebase')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          setFileTree(data.files);
          // Set a default file to show
          const findFirstFile = (nodes: FileNode[]): FileNode | null => {
            for (const n of nodes) {
              if (n.type === 'file') return n;
              if (n.children) {
                const f = findFirstFile(n.children);
                if (f) return f;
              }
            }
            return null;
          };
          const first = findFirstFile(data.files);
          if (first) setSelectedFile(first);
        } else {
          setError(data.message || 'Failed to load codebase');
        }
        setLoading(false);
      })
      .catch(err => {
        setError(err.message || 'Failed to connect to backend api');
        setLoading(false);
      });
  }, []);

  const handleCopy = () => {
    if (selectedFile?.content) {
      navigator.clipboard.writeText(selectedFile.content);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  const toggleDir = (dirName: string) => {
    setExpandedDirs(prev => ({
      ...prev,
      [dirName]: !prev[dirName]
    }));
  };

  const renderTree = (nodes: FileNode[], depth = 0, currentPath = '') => {
    return nodes.map((node, index) => {
      const nodePath = currentPath ? `${currentPath}/${node.name}` : node.name;
      const isExpanded = expandedDirs[nodePath];

      if (node.type === 'directory') {
        return (
          <div key={index} className="select-none">
            <button
              onClick={() => toggleDir(nodePath)}
              className="w-full flex items-center gap-2 py-1 px-2 hover:bg-brand-border/50 rounded text-left text-xs font-semibold text-[#a0a0a0] transition"
              style={{ paddingLeft: `${depth * 12 + 8}px` }}
            >
              <Folder className={`w-3.5 h-3.5 ${isExpanded ? 'text-brand-accent' : 'text-gray-400'}`} />
              <span>{node.name}</span>
            </button>
            {isExpanded && node.children && (
              <div className="border-l border-brand-border ml-3.5 pl-1.5 mt-0.5 mb-1">
                {renderTree(node.children, depth + 1, nodePath)}
              </div>
            )}
          </div>
        );
      } else {
        const isSelected = selectedFile?.path === node.path;
        return (
          <button
            key={index}
            onClick={() => setSelectedFile(node)}
            className={`w-full flex items-center gap-2 py-1 px-2 text-left text-xs rounded transition ${
              isSelected ? 'bg-brand-border text-brand-accent border-l-2 border-brand-accent' : 'hover:bg-brand-border/30 text-gray-300'
            }`}
            style={{ paddingLeft: `${depth * 12 + 8}px` }}
          >
            <File className={`w-3.5 h-3.5 ${isSelected ? 'text-brand-accent' : 'text-gray-400'}`} />
            <span className="truncate">{node.name}</span>
          </button>
        );
      }
    });
  };

  return (
    <div className="bg-brand-card border border-brand-border rounded-2xl overflow-hidden shadow-2xl">
      <div className="border-b border-brand-border px-5 py-4 bg-brand-card/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h3 className="text-base font-semibold text-white font-display flex items-center gap-2">
            <Code className="w-5 h-5 text-brand-accent" />
            PHP 8+ MVC Source Code Explorer (cPanel Ready)
          </h3>
          <p className="text-xs text-gray-400 mt-1">
            Browse and download the complete normalized, secure PHP OOP code compiled for deployment on cPanel hosting.
          </p>
        </div>
        <div className="flex gap-2">
          <a
            href="https://ais-dev-br2m4agc42p5hx7ddb3oyi-69729505955.us-east5.run.app/export"
            onClick={(e) => {
              // Create virtual zip on client or direct file downloads
              alert("Your browser is running in an iframe. To export the full PHP files, use the 'Export to ZIP' feature in the settings menu of Google AI Studio, or copy files directly from this code explorer.");
            }}
            className="flex items-center gap-1.5 text-xs font-semibold bg-brand-accent hover:opacity-90 text-black px-3 py-1.5 rounded-lg transition"
          >
            <Download className="w-3.5 h-3.5" />
            ZIP Export Instructions
          </a>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-12 min-h-[550px] bg-brand-bg">
        {/* File Tree Column */}
        <div className="col-span-1 md:col-span-3 border-r border-brand-border bg-brand-card/20 p-3 max-h-[600px] overflow-y-auto custom-scroll">
          <div className="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 px-2">PHP MVC Directories</div>
          {loading ? (
            <div className="flex items-center justify-center py-10 text-gray-500 text-xs">Loading tree...</div>
          ) : error ? (
            <div className="flex items-center gap-1 text-red-400 text-xs py-10 px-2">
              <AlertCircle className="w-4 h-4 shrink-0" />
              <span>{error}</span>
            </div>
          ) : (
            <div className="space-y-1">{renderTree(fileTree)}</div>
          )}
        </div>

        {/* Code Content Column */}
        <div className="col-span-1 md:col-span-9 bg-brand-bg flex flex-col max-h-[600px] overflow-hidden">
          {selectedFile ? (
            <>
              {/* Toolbar */}
              <div className="border-b border-brand-border bg-brand-card/40 px-4 py-2 flex items-center justify-between">
                <span className="font-mono text-xs text-brand-accent truncate">
                  {selectedFile.path || selectedFile.name}
                </span>
                <button
                  onClick={handleCopy}
                  className="flex items-center gap-1 text-xs hover:bg-brand-border text-gray-300 hover:text-white px-2.5 py-1 rounded-lg transition border border-brand-border"
                >
                  {copied ? (
                    <>
                      <Check className="w-3.5 h-3.5 text-green-400" />
                      <span className="text-green-400">Copied!</span>
                    </>
                  ) : (
                    <>
                      <Copy className="w-3.5 h-3.5" />
                      <span>Copy Code</span>
                    </>
                  )}
                </button>
              </div>

              {/* Code viewer container */}
              <div className="flex-1 overflow-auto custom-scroll p-4 font-mono text-xs text-gray-300 leading-relaxed bg-[#0c0c0c] whitespace-pre select-text selection:bg-[#333] selection:text-white">
                <code>{selectedFile.content}</code>
              </div>
            </>
          ) : (
            <div className="flex-1 flex flex-col items-center justify-center text-gray-500 py-20 px-4">
              <Code className="w-12 h-12 text-gray-600 mb-2" />
              <p className="text-xs">Select a PHP controller, view, or model from the tree on the left to review its source code.</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
